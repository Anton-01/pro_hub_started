@extends('admin.layouts.app')

@section('title', 'Directorio')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Directorio de Contactos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Contactos</li>
                </ol>
            </nav>
        </div>

        <div>
            <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Contacto
            </a>
            <a href="{{ route('admin.contacts.import') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Importar
            </a>
        </div>

    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.contacts.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, email, departamento..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">Todos los departamentos</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
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

{{-- Contacts Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" data-sortable="{{ route('admin.contacts.reorder') }}">
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th>Contacto</th>
                        <th>Departamento</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr data-id="{{ $contact->id }}">
                            <td>
                                <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($contact->avatar_url)
                                        <img src="{{ Storage::url($contact->avatar_url) }}" class="avatar-sm" alt="">
                                    @else
                                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="font-size: 0.75rem;">
                                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $contact->name }} {{ $contact->last_name }}</strong>
                                        @if($contact->position)
                                            <div class="small text-muted">{{ $contact->position }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $contact->department ?? '-' }}</td>
                            <td class="small">
                                @if($contact->email)
                                    <div><i class="fas fa-envelope me-1 text-muted"></i>{{ $contact->email }}</div>
                                @endif
                                @if($contact->phone)
                                    <div><i class="fas fa-phone me-1 text-muted"></i>{{ $contact->phone }}@if($contact->extension) ext. {{ $contact->extension }}@endif</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $contact->status }}">
                                    {{ ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Eliminar este contacto?">
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
                                    <i class="fas fa-address-book"></i>
                                    <h5>No hay contactos</h5>
                                    <p>Agrega contactos al directorio de tu empresa.</p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.contacts.import') }}" class="btn btn-success">
                                            <i class="fas fa-file-excel me-2"></i>Importar
                                        </a>
                                        <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Nuevo Contacto
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($contacts->hasPages())
        <div class="card-footer">
            {{ $contacts->links() }}
        </div>
    @endif
</div>
@endsection
