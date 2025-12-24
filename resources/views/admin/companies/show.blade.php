@extends('admin.layouts.app')

@section('title', $company->name)

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>{{ $company->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Empresas</a></li>
                    <li class="breadcrumb-item active">{{ $company->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <form action="{{ route('admin.companies.toggle-status', $company) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-{{ $company->status == 'active' ? 'danger' : 'success' }}">
                    <i class="fas fa-{{ $company->status == 'active' ? 'ban' : 'check' }} me-2"></i>
                    {{ $company->status == 'active' ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    {{-- Company Info --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($company->name, 0, 1)) }}
                    </div>
                </div>
                <h5 class="mb-1">{{ $company->name }}</h5>
                <p class="text-muted mb-3">{{ $company->slug }}</p>
                <span class="badge badge-status-{{ $company->status }} fs-6">
                    {{ ucfirst($company->status) }}
                </span>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Información de Contacto</div>
            <div class="card-body">
                @if($company->email)
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                    </div>
                @endif
                @if($company->phone)
                    <div class="mb-3">
                        <small class="text-muted d-block">Teléfono</small>
                        {{ $company->phone }}
                    </div>
                @endif
                @if($company->website)
                    <div class="mb-3">
                        <small class="text-muted d-block">Sitio Web</small>
                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                    </div>
                @endif
                @if($company->address)
                    <div class="mb-3">
                        <small class="text-muted d-block">Dirección</small>
                        {{ $company->address }}
                    </div>
                @endif
                @if($company->tax_id)
                    <div>
                        <small class="text-muted d-block">RFC / Tax ID</small>
                        {{ $company->tax_id }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Estadísticas</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Usuarios</span>
                        <strong>{{ $company->users_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Módulos</span>
                        <strong>{{ $company->modules_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Contactos</span>
                        <strong>{{ $company->contacts_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Eventos</span>
                        <strong>{{ $company->calendar_events_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Noticias</span>
                        <strong>{{ $company->news_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Banners</span>
                        <strong>{{ $company->banner_images_count }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Users --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex">
                <span>Usuarios de la Empresa</span>
                <span class="badge bg-primary ms-2">{{ $company->users->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($user->avatar_url)
                                                <img src="{{ Storage::url($user->avatar_url) }}" class="avatar-sm" alt="">
                                            @else
                                                <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="font-size: 0.75rem;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                {{ $user->full_name }}
                                                @if($user->is_primary_admin)
                                                    <span class="badge bg-warning text-dark ms-1">Principal</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $user->status }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
