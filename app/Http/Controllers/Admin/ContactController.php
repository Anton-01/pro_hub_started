<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $contacts = $query->orderBy('sort_order')
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
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'extension' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $lastOrder = Contact::where('company_id', $this->getCompanyId())
            ->max('sort_order') ?? 0;

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
            'sort_order' => $lastOrder + 1,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto creado correctamente.');
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
    public function update(Request $request, Contact $contact)
    {
        $this->authorizeAccess($contact);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'extension' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

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

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto actualizado correctamente.');
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

        return back()->with('success', 'Estado del contacto actualizado.');
    }

    private function authorizeAccess(Contact $contact): void
    {
        if ($contact->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a este contacto.');
        }
    }
}
