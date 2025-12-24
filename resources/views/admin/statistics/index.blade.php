@extends('admin.layouts.app')

@section('title', 'Estadísticas')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Estadísticas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Estadísticas</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary" data-period="week">
                <i class="fas fa-calendar-week me-1"></i>Semana
            </button>
            <button type="button" class="btn btn-outline-secondary active" data-period="month">
                <i class="fas fa-calendar-alt me-1"></i>Mes
            </button>
            <button type="button" class="btn btn-outline-secondary" data-period="year">
                <i class="fas fa-calendar me-1"></i>Año
            </button>
        </div>
    </div>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
                        <div class="stat-label">Usuarios Totales</div>
                    </div>
                </div>
                <div class="stat-trend text-success mt-2">
                    <i class="fas fa-arrow-up me-1"></i>
                    <span>{{ $stats['users_growth'] ?? 0 }}% este mes</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-value">{{ $stats['active_sessions'] ?? 0 }}</div>
                        <div class="stat-label">Sesiones Activas</div>
                    </div>
                </div>
                <div class="stat-trend text-muted mt-2">
                    <i class="fas fa-clock me-1"></i>
                    <span>Última hora</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-value">{{ $stats['total_events'] ?? 0 }}</div>
                        <div class="stat-label">Eventos del Mes</div>
                    </div>
                </div>
                <div class="stat-trend text-info mt-2">
                    <i class="fas fa-calendar-day me-1"></i>
                    <span>{{ $stats['upcoming_events'] ?? 0 }} próximos</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-value">{{ $stats['active_news'] ?? 0 }}</div>
                        <div class="stat-label">Noticias Activas</div>
                    </div>
                </div>
                <div class="stat-trend text-warning mt-2">
                    <i class="fas fa-star me-1"></i>
                    <span>{{ $stats['priority_news'] ?? 0 }} prioritarias</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Activity Chart --}}
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Actividad de Usuarios</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Últimos 30 días
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Últimos 7 días</a></li>
                        <li><a class="dropdown-item active" href="#">Últimos 30 días</a></li>
                        <li><a class="dropdown-item" href="#">Últimos 90 días</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="300"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Actividad Reciente</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentActivity ?? [] as $activity)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon me-3">
                                    @switch($activity->action)
                                        @case('create')
                                            <i class="fas fa-plus-circle text-success"></i>
                                            @break
                                        @case('update')
                                            <i class="fas fa-edit text-primary"></i>
                                            @break
                                        @case('delete')
                                            <i class="fas fa-trash text-danger"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle text-muted"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small">
                                        <strong>{{ $activity->user->name ?? 'Sistema' }}</strong>
                                        {{ $activity->description }}
                                    </div>
                                    <div class="text-muted smaller">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No hay actividad reciente
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('admin.activity-logs.index') }}" class="small">Ver todo el historial</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Content Stats --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Contenido por Tipo</h5>
            </div>
            <div class="card-body">
                <canvas id="contentChart" height="250"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Users --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Usuarios Más Activos</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Acciones</th>
                            <th>Último Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topUsers ?? [] as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div>{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->actions_count }}</td>
                                <td class="small text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No hay datos disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stat-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1;
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.875rem;
    }
    .stat-trend {
        font-size: 0.8rem;
    }
    .smaller {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Activity Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels ?? []) !!},
        datasets: [{
            label: 'Acciones',
            data: {!! json_encode($chartData ?? []) !!},
            borderColor: '#c9a227',
            backgroundColor: 'rgba(201, 162, 39, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Content Chart
const contentCtx = document.getElementById('contentChart').getContext('2d');
new Chart(contentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Eventos', 'Noticias', 'Contactos', 'Banners'],
        datasets: [{
            data: [
                {{ $stats['total_events'] ?? 0 }},
                {{ $stats['active_news'] ?? 0 }},
                {{ $stats['total_contacts'] ?? 0 }},
                {{ $stats['total_banners'] ?? 0 }}
            ],
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
@endsection
