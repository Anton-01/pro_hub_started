<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Imports\ContactsImport;
use App\Exports\ContactsTemplateExport;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mckenziearts\Notify\Exceptions\InvalidNotificationException;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class ContactController extends Controller
{
    private function getCompanyId(): string
    {
        return auth()->user()->company_id;
    }

    /**
     * Listar contactos
     */
    public function index(Request $request)
    {
        $query = Contact::where('company_id', $this->getCompanyId());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('department', 'ilike', "%{$search}%")
                    ->orWhere('position', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->orderBy('order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Obtener departamentos únicos para el filtro
        $departments = Contact::where('company_id', $this->getCompanyId())
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.contacts.index', compact('contacts', 'departments'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $departments = Contact::where('company_id', $this->getCompanyId())
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.contacts.create', compact('departments'));
    }

    /**
     * Guardar nuevo contacto
     * @throws InvalidNotificationException
     */
    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $lastOrder = Contact::where('company_id', $this->getCompanyId())
            ->max('order') ?? 0;

        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            $avatarUrl = $request->file('avatar')->store('contacts', 'public');
        }

        Contact::create([
            'company_id' => $this->getCompanyId(),
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'department' => $validated['department'] ?? null,
            'position' => $validated['position'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'extension' => $validated['extension'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'avatar_url' => $avatarUrl,
            'order' => $lastOrder + 1,
            'status' => $validated['status'],
        ]);

        notify()->success()->message('Contacto creado correctamente.')->send();
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Mostrar contacto
     */
    public function show(Contact $contact)
    {
        $this->authorizeAccess($contact);

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Contact $contact)
    {
        $this->authorizeAccess($contact);

        $departments = Contact::where('company_id', $this->getCompanyId())
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.contacts.edit', compact('contact', 'departments'));
    }

    /**
     * Actualizar contacto
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        $this->authorizeAccess($contact);

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'department' => $validated['department'] ?? null,
            'position' => $validated['position'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'extension' => $validated['extension'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'status' => $validated['status'],
        ];

        if ($request->hasFile('avatar')) {
            if ($contact->avatar_url) {
                Storage::disk('public')->delete($contact->avatar_url);
            }
            $updateData['avatar_url'] = $request->file('avatar')->store('contacts', 'public');
        }

        $contact->update($updateData);

        notify()->success()->message('Contacto actualizado correctamente.')->send();
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Eliminar contacto
     */
    public function destroy(Contact $contact)
    {
        $this->authorizeAccess($contact);

        if ($contact->avatar_url) {
            Storage::disk('public')->delete($contact->avatar_url);
        }

        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto eliminado correctamente.');
    }

    /**
     * Reordenar contactos
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'uuid|exists:contacts,id',
        ]);

        foreach ($request->order as $index => $id) {
            Contact::where('id', $id)
                ->where('company_id', $this->getCompanyId())
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus(Contact $contact)
    {
        $this->authorizeAccess($contact);

        $newStatus = $contact->status === 'active' ? 'inactive' : 'active';
        $contact->update(['status' => $newStatus]);

        notify()->success('Estado del contacto actualizado.', 'Éxito');
        return back();
    }

    /**
     * Descargar plantilla Excel para importación
     * @throws InvalidNotificationException
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(new ContactsTemplateExport(), 'planting_contacts.xlsx');
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            notify()->error()->message('Error al generar el archivo temporal.')->send();
            return back();
        }
    }

    /**
     * Mostrar vista de importación
     */
    public function importView()
    {
        return view('admin.contacts.import');
    }

    /**
     * Procesar importación de contactos desde Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB máximo
        ], [
            'file.required' => 'Debes seleccionar un archivo.',
            'file.mimes' => 'El archivo debe ser un Excel (.xlsx, .xls) o CSV.',
            'file.max' => 'El archivo no puede exceder 10MB.',
        ]);

        try {
            $import = new ContactsImport($this->getCompanyId());

            Excel::import($import, $request->file('file'));

            $summary = $import->getSummary();
            $errors = $import->getErrors();

            if ($errors) {
                return redirect()->route('admin.contacts.import')
                    ->with('import_summary', $summary)
                    ->with('import_errors', $errors)
                    ->with('warning', "Importación completada con errores: {$summary['success']} creados, {$summary['updated']} actualizados, {$summary['errors']} errores.");
            }
            notify()->success()->message("Importación exitosa: {$summary['success']} contactos creados, {$summary['updated']} actualizados.")->send();
            return redirect()->route('admin.contacts.index');
        } catch (\Exception $e) {
            return redirect()->route('admin.contacts.import')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function authorizeAccess(Contact $contact): void
    {
        if ($contact->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a este contacto.');
        }
    }
}
