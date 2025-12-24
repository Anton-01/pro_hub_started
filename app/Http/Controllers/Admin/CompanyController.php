<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Listar todas las empresas
     */
    public function index(Request $request)
    {
        $query = Company::withCount(['users', 'modules', 'contacts', 'news', 'calendarEvents']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('slug', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Guardar nueva empresa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:companies,slug',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'max_admins' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:active,inactive,pending',
            // Datos del admin primario
            'admin_name' => 'required|string|max:255',
            'admin_last_name' => 'nullable|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Generar slug si no se proporciona
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Crear empresa
        $company = Company::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'tax_id' => $validated['tax_id'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'max_admins' => $validated['max_admins'] ?? 3,
            'status' => $validated['status'],
        ]);

        // Crear admin primario
        User::create([
            'company_id' => $company->id,
            'name' => $validated['admin_name'],
            'last_name' => $validated['admin_last_name'] ?? null,
            'email' => $validated['admin_email'],
            'password' => $validated['admin_password'],
            'role' => 'admin',
            'status' => 'active',
            'is_primary_admin' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa creada correctamente.');
    }

    /**
     * Mostrar detalle de empresa
     */
    public function show(Company $company)
    {
        $company->load([
            'users' => fn($q) => $q->orderBy('role')->orderBy('name'),
            'configuration',
        ]);

        $company->loadCount(['users', 'modules', 'contacts', 'news', 'calendarEvents', 'bannerImages']);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Actualizar empresa
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('companies')->ignore($company->id)],
            'tax_id' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'max_admins' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:active,inactive,pending,suspended',
        ]);

        $company->update($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Eliminar empresa
     */
    public function destroy(Company $company)
    {
        // Verificar que no sea la única empresa
        if (Company::count() <= 1) {
            return back()->with('error', 'No puedes eliminar la única empresa del sistema.');
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa eliminada correctamente.');
    }

    /**
     * Cambiar estado de empresa
     */
    public function toggleStatus(Company $company)
    {
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $company->update(['status' => $newStatus]);

        return back()->with('success', 'Estado de la empresa actualizado.');
    }
}
