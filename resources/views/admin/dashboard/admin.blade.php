@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <h1>Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Inicio</li>
        </ol>
    </nav>
@endsection

@section('content')
{{-- Stats Cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary-light">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['total_users']) }}</div>
                <div class="stats-label">Usuarios</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-success-light">
                <i class="fas fa-th-large"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['active_modules']) }}</div>
                <div class="stats-label">Módulos Activos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-info-light">
                <i class="fas fa-address-book"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['total_contacts']) }}</div>
                <div class="stats-label">Contactos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning-light">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['upcoming_events']) }}</div>
                <div class="stats-label">Eventos Próximos</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Upcoming Events --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex">
                <span><i class="fas fa-calendar-alt me-2"></i>Próximos Eventos</span>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-light ms-auto">
                    Ver todos
                </a>
            </div>
            <div class="card-body p-0">
                @if($upcomingEvents->isEmpty())
                    <div class="empty-state py-4">
                        <i class="fas fa-calendar-times"></i>
                        <p class="mb-0">No hay eventos próximos</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($upcomingEvents as $event)
                            <a href="{{ route('admin.events.edit', $event) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-center" style="min-width: 50px;">
                                        <div class="fw-bold text-primary" style="font-size: 1.5rem;">
                                            {{ $event->event_date->format('d') }}
                                        </div>
                                        <small class="text-muted text-uppercase">
                                            {{ $event->event_date->format('M') }}
                                        </small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $event->title }}</div>
                                        @if($event->start_time)
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $event->start_time }}
                                                @if($event->end_time)
                                                    - {{ $event->end_time }}
                                                @endif
                                            </small>
                                        @elseif($event->is_all_day)
                                            <small class="text-muted">Todo el día</small>
                                        @endif
                                    </div>
                                    <div style="width: 12px; height: 12px; border-radius: 50%; background: {{ $event->color ?? '#3b82f6' }};"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Active News --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex">
                <span><i class="fas fa-newspaper me-2"></i>Noticias Activas</span>
                <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-light ms-auto">
                    Ver todas
                </a>
            </div>
            <div class="card-body p-0">
                @if($activeNews->isEmpty())
                    <div class="empty-state py-4">
                        <i class="fas fa-newspaper"></i>
                        <p class="mb-0">No hay noticias activas</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($activeNews as $news)
                            <a href="{{ route('admin.news.edit', $news) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-start gap-2">
                                    @if($news->is_priority)
                                        <span class="badge bg-danger">Prioritaria</span>
                                    @endif
                                    <div class="flex-grow-1">
                                        <p class="mb-1">{{ Str::limit($news->text, 100) }}</p>
                                        @if($news->url)
                                            <small class="text-muted">
                                                <i class="fas fa-link me-1"></i>
                                                {{ Str::limit($news->url, 40) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent Users --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex">
                <span><i class="fas fa-users me-2"></i>Últimos Usuarios</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light ms-auto">
                    Ver todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
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
                                                <div>{{ $user->full_name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $user->status }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
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

    {{-- Recent Activity --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-2"></i>Actividad Reciente
            </div>
            <div class="card-body p-0">
                @if($recentActivity->isEmpty())
                    <div class="empty-state py-4">
                        <i class="fas fa-clipboard-list"></i>
                        <p class="mb-0">No hay actividad reciente</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentActivity as $log)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="badge bg-light text-dark">{{ $log->action }}</span>
                                        <span class="ms-1">{{ $log->entity_type }}</span>
                                    </div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <small class="text-muted">
                                    Por: {{ $log->user->name ?? 'Sistema' }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
