@extends('admin.layouts.app')

@section('title', 'Módulos')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Módulos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Módulos</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.modules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Módulo
        </a>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.modules.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Enlace</option>
                    <option value="modal" {{ request('type') == 'modal' ? 'selected' : '' }}>Modal</option>
                    <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>Externo</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
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

{{-- Modules Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" data-sortable="{{ route('admin.modules.reorder') }}">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th>Módulo</th>
                        <th>Tipo</th>
                        <th>URL</th>
                        <th>Estado</th>
                        <th width="140">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $module)
                        <tr data-id="{{ $module->id }}">
                            <td>
                                <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($module->icon)
                                        <i class="{{ $module->icon }}" style="width: 24px; text-align: center; color: {{ $module->background_color ?? '#6c757d' }};"></i>
                                    @else
                                        <i class="fas fa-cube" style="width: 24px; text-align: center; color: #6c757d;"></i>
                                    @endif
                                    <div>
                                        <strong>{{ $module->label }}</strong>
                                        @if($module->highlight)
                                            <span class="badge bg-warning text-dark ms-1">Destacado</span>
                                        @endif
                                        @if($module->description)
                                            <div class="small text-muted">{{ Str::limit($module->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $module->type }}</span>
                            </td>
                            <td class="small text-muted">
                                {{ Str::limit($module->url, 40) }}
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $module->status }}">
                                    {{ ucfirst($module->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.modules.toggle-status', $module) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $module->status == 'active' ? 'warning' : 'success' }}" title="{{ $module->status == 'active' ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-{{ $module->status == 'active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Eliminar este módulo?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-th-large"></i>
                                    <h5>No hay módulos</h5>
                                    <p>Crea módulos para el portal de tu empresa.</p>
                                    <a href="{{ route('admin.modules.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Módulo
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($modules->hasPages())
        <div class="card-footer">
            {{ $modules->links() }}
        </div>
    @endif
</div>

<p class="small text-muted mt-3">
    <i class="fas fa-info-circle me-1"></i>
    Arrastra las filas para cambiar el orden de los módulos.
</p>
@endsection
