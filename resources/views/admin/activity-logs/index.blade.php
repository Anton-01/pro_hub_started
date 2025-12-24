@extends('admin.layouts.app')

@section('title', 'Registro de Actividad')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Registro de Actividad</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Actividad</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-outline-secondary" id="export-btn">
            <i class="fas fa-download me-2"></i>Exportar
        </button>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">Todos los usuarios</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="action" class="form-select">
                    <option value="">Todas las acciones</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Crear</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Actualizar</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Eliminar</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="entity_type" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="users" {{ request('entity_type') == 'users' ? 'selected' : '' }}>Usuarios</option>
                    <option value="calendar_events" {{ request('entity_type') == 'calendar_events' ? 'selected' : '' }}>Eventos</option>
                    <option value="news" {{ request('entity_type') == 'news' ? 'selected' : '' }}>Noticias</option>
                    <option value="contacts" {{ request('entity_type') == 'contacts' ? 'selected' : '' }}>Contactos</option>
                    <option value="banner_images" {{ request('entity_type') == 'banner_images' ? 'selected' : '' }}>Banners</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="Desde" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="Hasta" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Activity Timeline --}}
<div class="card">
    <div class="card-body">
        @if(isset($logs) && $logs->count() > 0)
            <div class="activity-timeline">
                @php $currentDate = null; @endphp
                @foreach($logs as $log)
                    @if($currentDate !== $log->created_at->format('Y-m-d'))
                        @php $currentDate = $log->created_at->format('Y-m-d'); @endphp
                        <div class="timeline-date">
                            <span class="badge bg-light text-dark">
                                {{ $log->created_at->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                    @endif
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            @switch($log->action)
                                @case('create')
                                    <i class="fas fa-plus-circle text-success"></i>
                                    @break
                                @case('update')
                                    <i class="fas fa-edit text-primary"></i>
                                    @break
                                @case('delete')
                                    <i class="fas fa-trash text-danger"></i>
                                    @break
                                @case('login')
                                    <i class="fas fa-sign-in-alt text-info"></i>
                                    @break
                                @case('logout')
                                    <i class="fas fa-sign-out-alt text-secondary"></i>
                                    @break
                                @default
                                    <i class="fas fa-circle text-muted"></i>
                            @endswitch
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $log->user->name ?? 'Sistema' }}</strong>
                                    <span class="text-muted">{{ $log->description }}</span>
                                    @if($log->entity_type)
                                        <span class="badge bg-secondary ms-1">{{ ucfirst(str_replace('_', ' ', $log->entity_type)) }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                            </div>
                            @if($log->old_values || $log->new_values)
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $log->id }}">
                                        <i class="fas fa-info-circle me-1"></i>Ver detalles
                                    </button>
                                    <div class="collapse mt-2" id="details-{{ $log->id }}">
                                        <div class="row small">
                                            @if($log->old_values)
                                                <div class="col-md-6">
                                                    <div class="text-muted mb-1">Valores anteriores:</div>
                                                    <pre class="bg-light p-2 rounded mb-0">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                            @if($log->new_values)
                                                <div class="col-md-6">
                                                    <div class="text-muted mb-1">Valores nuevos:</div>
                                                    <pre class="bg-light p-2 rounded mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($log->ip_address)
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-globe me-1"></i>{{ $log->ip_address }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h5>No hay registros de actividad</h5>
                <p>La actividad del sistema aparecerá aquí.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-date {
        margin: 20px 0 10px -30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        display: flex;
        gap: 15px;
    }
    .timeline-icon {
        position: absolute;
        left: -30px;
        width: 22px;
        height: 22px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-content {
        flex: 1;
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
    }
    .timeline-content pre {
        font-size: 0.75rem;
        max-height: 150px;
        overflow: auto;
    }
</style>
@endpush
@endsection
