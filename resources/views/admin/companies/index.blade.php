@extends('admin.layouts.app')

@section('title', 'Empresas')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Empresas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Empresas</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Empresa
        </a>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.companies.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, email o slug..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
            </div>
            @if(request()->hasAny(['search', 'status']))
                <div class="col-md-2">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Companies Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>Usuarios</th>
                        <th>Módulos</th>
                        <th>Estado</th>
                        <th>Creada</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $company->name }}</strong>
                                    <div class="small text-muted">{{ $company->slug }}</div>
                                </div>
                            </td>
                            <td>{{ $company->email }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $company->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $company->modules_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $company->status }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $company->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Estás seguro de eliminar esta empresa? Esta acción no se puede deshacer.">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-building"></i>
                                    <h5>No hay empresas</h5>
                                    <p>Comienza creando la primera empresa del sistema.</p>
                                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nueva Empresa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($companies->hasPages())
        <div class="card-footer">
            {{ $companies->links() }}
        </div>
    @endif
</div>
@endsection
