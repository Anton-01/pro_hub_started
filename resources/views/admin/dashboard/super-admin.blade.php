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
                <i class="fas fa-building"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['total_companies']) }}</div>
                <div class="stats-label">Empresas Totales</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-success-light">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['active_companies']) }}</div>
                <div class="stats-label">Empresas Activas</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-info-light">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['total_users']) }}</div>
                <div class="stats-label">Usuarios Totales</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning-light">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ number_format($stats['total_admins']) }}</div>
                <div class="stats-label">Administradores</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- User Registration Chart --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex">
                <span>Registros por Mes</span>
                <a href="{{ route('admin.statistics.users') }}" class="btn btn-sm btn-light ms-auto">
                    Ver más
                </a>
            </div>
            <div class="card-body">
                <canvas id="registrationsChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Companies by Status --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Empresas por Estado</div>
            <div class="card-body">
                <canvas id="companiesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent Companies --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex">
                <span>Últimas Empresas</span>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-light ms-auto">
                    Ver todas
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompanies as $company)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.companies.show', $company) }}">
                                            {{ $company->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $company->status }}">
                                            {{ ucfirst($company->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $company->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No hay empresas registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex">
                <span>Últimos Usuarios</span>
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
                                <th>Empresa</th>
                                <th>Rol</th>
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
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $user->full_name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">{{ $user->company->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $user->role }}</span>
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
</div>

{{-- Recent Activity --}}
<div class="card">
    <div class="card-header d-flex">
        <span>Actividad Reciente</span>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-light ms-auto">
            Ver todo
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Usuario</th>
                        <th>Empresa</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $log)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $log->action }}</span>
                                {{ $log->entity_type }}
                            </td>
                            <td>{{ $log->user->name ?? 'Sistema' }}</td>
                            <td class="small">{{ $log->company->name ?? '-' }}</td>
                            <td class="text-muted small">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay actividad reciente
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Registrations Chart
    const regCtx = document.getElementById('registrationsChart').getContext('2d');
    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($usersByMonth)) !!},
            datasets: [{
                label: 'Usuarios',
                data: {!! json_encode(array_values($usersByMonth)) !!},
                borderColor: '#4c78dd',
                backgroundColor: 'rgba(76, 120, 221, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
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

    // Companies Chart
    const compCtx = document.getElementById('companiesChart').getContext('2d');
    new Chart(compCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map('ucfirst', array_keys($companiesByStatus))) !!},
            datasets: [{
                data: {!! json_encode(array_values($companiesByStatus)) !!},
                backgroundColor: ['#46c37b', '#d26a5c', '#f3b760', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
