@extends('admin.layouts.app')

@section('title', 'Usuarios')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Usuarios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->canCreateAdmins())
                <a href="{{ route('admin.users.create-admin') }}" class="btn btn-outline-primary">
                    <i class="fas fa-user-shield me-2"></i>Nuevo Admin
                </a>
            @endif
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Usuario
            </a>
        </div>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
            </div>
            @if(auth()->user()->isSuperAdmin() && $companies->isNotEmpty())
                <div class="col-md-3">
                    <select name="company_id" class="form-select">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="">Todos los roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Usuario</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        @if(auth()->user()->isSuperAdmin())
                            <th>Empresa</th>
                        @endif
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar_url)
                                        <img src="{{ Storage::url($user->avatar_url) }}" class="avatar-sm" alt="">
                                    @else
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="font-size: 0.75rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div>
                                            {{ $user->full_name }}
                                            @if($user->is_primary_admin)
                                                <span class="badge bg-warning text-dark ms-1" title="Administrador Principal">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            @if(auth()->user()->isSuperAdmin())
                                <td class="small">{{ $user->company->name ?? '-' }}</td>
                            @endif
                            <td>
                                <span class="badge bg-{{ $user->role == 'super_admin' ? 'danger' : ($user->role == 'admin' ? 'primary' : 'secondary') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                @if($user->id !== auth()->id() && !$user->is_primary_admin)
                                    <label class="status-toggle" data-toggle-url="{{ route('admin.users.toggle-status', $user) }}" title="{{ $user->status == 'active' ? 'Clic para desactivar' : 'Clic para activar' }}">
                                        <input type="checkbox" {{ $user->status == 'active' ? 'checked' : '' }}>
                                        <span class="toggle-switch"></span>
                                    </label>
                                    <small class="status-text d-block text-muted" style="font-size: 0.7rem; margin-top: 2px;">{{ $user->status == 'active' ? 'Activo' : 'Inactivo' }}</small>
                                @else
                                    <span class="badge badge-status-{{ $user->status }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id() && !$user->is_primary_admin)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Estás seguro de eliminar este usuario?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h5>No hay usuarios</h5>
                                    <p>Comienza creando el primer usuario.</p>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
