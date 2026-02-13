@extends('admin.layouts.app')

@section('title', 'Eventos')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Calendario de Eventos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Eventos</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Evento
        </a>
    </div>
@endsection

@section('content')
{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.events.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar por título..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="month" class="form-select">
                    <option value="">Todos los meses</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select">
                    @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                        <option value="{{ $y }}" {{ (request('year') ?? date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
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

{{-- Events Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Evento</th>
                        <th>Horario</th>
                        <th>Color</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="event-date-badge" style="background-color: {{ $event->color }}20; color: {{ $event->color }};">
                                        <div class="event-day">{{ $event->event_date->format('d') }}</div>
                                        <div class="event-month">{{ $event->event_date->translatedFormat('M') }}</div>
                                    </div>
                                    <span class="small text-muted">{{ $event->event_date->format('Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $event->title }}</strong>
                                @if($event->description)
                                    <div class="small text-muted">{{ Str::limit($event->description, 60) }}</div>
                                @endif
                                @if($event->is_recurring)
                                    <span class="badge bg-info small"><i class="fas fa-sync-alt me-1"></i>Recurrente</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($event->is_all_day)
                                    <span class="text-muted">Todo el día</span>
                                @else
                                    @if($event->start_time)
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                    @endif
                                    @if($event->end_time)
                                        - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span class="color-preview" style="background-color: {{ $event->color }};"></span>
                            </td>
                            <td>
                                <label class="status-toggle" data-toggle-url="{{ route('admin.events.toggle-status', $event) }}" title="{{ $event->status == 'active' ? 'Clic para desactivar' : 'Clic para activar' }}">
                                    <input type="checkbox" {{ $event->status == 'active' ? 'checked' : '' }}>
                                    <span class="toggle-switch"></span>
                                </label>
                                <small class="status-text d-block text-muted" style="font-size: 0.7rem; margin-top: 2px;">{{ $event->status == 'active' ? 'Activo' : 'Inactivo' }}</small>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" data-confirm="¿Eliminar este evento?">
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
                                    <i class="fas fa-calendar-alt"></i>
                                    <h5>No hay eventos</h5>
                                    <p>Crea eventos para el calendario de tu empresa.</p>
                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Nuevo Evento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($events->hasPages())
        <div class="card-footer">
            {{ $events->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .event-date-badge {
        width: 50px;
        text-align: center;
        padding: 5px;
        border-radius: 8px;
    }
    .event-date-badge .event-day {
        font-size: 1.25rem;
        font-weight: bold;
        line-height: 1;
    }
    .event-date-badge .event-month {
        font-size: 0.7rem;
        text-transform: uppercase;
    }
    .color-preview {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,0.1);
    }
</style>
@endpush
@endsection
