<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Mail\WelcomeUserEmail;
use App\Mail\WelcomeAdminEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Mckenziearts\Notify\Exceptions\InvalidNotificationException;

class UserController extends Controller
{
    /**
     * Obtener el company_id según el rol del usuario
     */
    private function getCompanyId(): ?string
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return request('company_id') ?? $user->company_id;
        }

        return $user->company_id;
    }

    /**
     * Listar usuarios
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = User::with('company');

        // Super admin ve todos, admin solo su empresa
        if (!$user->isSuperAdmin()) {
            $query->where('company_id', $user->company_id);
        } else {
            if ($request->filled('company_id')) {
                $query->where('company_id', $request->company_id);
            }
        }

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $companies = $user->isSuperAdmin() ? Company::orderBy('name')->get() : collect();

        return view('admin.users.index', compact('users', 'companies'));
    }

    /**
     * Mostrar formulario de creación de usuario
     */
    public function create()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::orderBy('name')->get() : collect();

        return view('admin.users.create', compact('companies'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'company_id' => $currentUser->isSuperAdmin() ? 'required|uuid|exists:companies,id' : 'nullable',
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                }),
            ],
            'phone' => 'nullable|string|max:50',
            'password' => ['required', 'confirmed', Password::min(8)],
            'status' => 'required|in:active,inactive,pending',
            'send_welcome_email' => 'boolean',
        ]);

        $user = User::create([
            'company_id' => $currentUser->isSuperAdmin() ? $validated['company_id'] : $companyId,
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => 'user',
            'status' => $validated['status'],
            'email_verified_at' => $validated['status'] === 'active' ? now() : null,
        ]);

        // Enviar email de bienvenida
        if ($request->boolean('send_welcome_email')) {
            Mail::to($user->email)->send(new WelcomeUserEmail($user));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Mostrar formulario para crear administrador
     */
    public function createAdmin()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::orderBy('name')->get() : collect();

        return view('admin.users.create-admin', compact('companies'));
    }

    /**
     * Guardar nuevo administrador
     */
    public function storeAdmin(Request $request)
    {
        $currentUser = auth()->user();
        $companyId = $this->getCompanyId();

        // Verificar límite de admins
        $company = Company::find($companyId);
        $currentAdmins = User::where('company_id', $companyId)
            ->where('role', 'admin')
            ->count();

        if ($currentAdmins >= $company->max_admins) {
            return back()->with('error', "Esta empresa ya tiene el máximo de administradores permitidos ({$company->max_admins}).");
        }

        $validated = $request->validate([
            'company_id' => $currentUser->isSuperAdmin() ? 'required|uuid|exists:companies,id' : 'nullable',
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                }),
            ],
            'phone' => 'nullable|string|max:50',
            'password' => ['required', 'confirmed', Password::min(8)],
            'send_welcome_email' => 'boolean',
        ]);

        $user = User::create([
            'company_id' => $currentUser->isSuperAdmin() ? $validated['company_id'] : $companyId,
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => 'admin',
            'status' => 'active',
            'is_primary_admin' => false,
            'email_verified_at' => now(),
        ]);

        if ($request->boolean('send_welcome_email')) {
            Mail::to($user->email)->send(new WelcomeAdminEmail($user));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Administrador creado correctamente.');
    }

    /**
     * Mostrar detalle de usuario
     */
    public function show(User $user)
    {
        $this->authorizeUserAccess($user);

        $user->load('company');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $this->authorizeUserAccess($user);

        $currentUser = auth()->user();
        $companies = $currentUser->isSuperAdmin() ? Company::orderBy('name')->get() : collect();

        return view('admin.users.edit', compact('user', 'companies'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeUserAccess($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($user) {
                    return $query->where('company_id', $user->company_id);
                })->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:50',
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'status' => 'required|in:active,inactive,pending,suspended',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        $this->authorizeUserAccess($user);

        // No permitir eliminar al admin primario
        if ($user->is_primary_admin) {
            notify()->error('No puedes eliminar al administrador primario de la empresa.', 'Error');
            return back();
        }

        // No permitir auto-eliminación
        if ($user->id === auth()->id()) {
            notify()->error('No puedes eliminarte a ti mismo.', 'Error');
            return back();
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Cambiar estado del usuario
     * @throws InvalidNotificationException
     */
    public function toggleStatus(Request $request, User $user)
    {
        $this->authorizeUserAccess($user);

        if ($user->id === auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivarte a ti mismo.'
                ], 422);
            }

            notify()->error()->message('No puedes desactivarte a ti mismo.')->send();
            return back();
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Estado actualizado correctamente'
            ]);
        }

        notify()->success()->message('Estado del usuario actualizado.')->send();
        return back();
    }

    /**
     * Mostrar perfil del usuario actual
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.users.profile', compact('user'));
    }

    /**
     * Actualizar perfil
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        notify()->success('Perfil actualizado correctamente.', 'Éxito');
        return back();
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => $validated['password'],
        ]);

        notify()->success('Contraseña actualizada correctamente.', 'Éxito');
        return back();
    }

    /**
     * Subir avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        // Eliminar avatar anterior
        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar_url' => $path]);

        notify()->success('Avatar actualizado correctamente.', 'Éxito');
        return back();
    }

    /**
     * Verificar acceso al usuario
     */
    private function authorizeUserAccess(User $user): void
    {
        $currentUser = auth()->user();

        if (!$currentUser->isSuperAdmin() && $user->company_id !== $currentUser->company_id) {
            abort(403, 'No tienes acceso a este usuario.');
        }
    }
}
