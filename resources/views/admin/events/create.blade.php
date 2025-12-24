@extends('admin.layouts.app')

@section('title', 'Nuevo Evento')

@section('page-header')
    <div>
        <h1>Nuevo Evento</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Eventos</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.events.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Evento</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido Detallado</label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="4">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Fecha y Hora</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Fecha del Evento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('event_date') is-invalid @enderror"
                                   id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                            @error('event_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="is_all_day" name="is_all_day" value="1"
                                       {{ old('is_all_day') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_all_day">Todo el día</label>
                            </div>
                        </div>
                    </div>

                    <div class="row time-fields" style="{{ old('is_all_day') ? 'display:none;' : '' }}">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                   id="start_time" name="start_time" value="{{ old('start_time') }}">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                   id="end_time" name="end_time" value="{{ old('end_time') }}">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_recurring" name="is_recurring" value="1"
                                   {{ old('is_recurring') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_recurring">Evento recurrente</label>
                        </div>
                    </div>

                    <div class="recurrence-fields" style="{{ old('is_recurring') ? '' : 'display:none;' }}">
                        <div class="mb-3">
                            <label for="recurrence_rule" class="form-label">Regla de Recurrencia</label>
                            <select class="form-select @error('recurrence_rule') is-invalid @enderror"
                                    id="recurrence_rule" name="recurrence_rule">
                                <option value="">Seleccionar...</option>
                                <option value="daily" {{ old('recurrence_rule') == 'daily' ? 'selected' : '' }}>Diariamente</option>
                                <option value="weekly" {{ old('recurrence_rule') == 'weekly' ? 'selected' : '' }}>Semanalmente</option>
                                <option value="monthly" {{ old('recurrence_rule') == 'monthly' ? 'selected' : '' }}>Mensualmente</option>
                                <option value="yearly" {{ old('recurrence_rule') == 'yearly' ? 'selected' : '' }}>Anualmente</option>
                            </select>
                            @error('recurrence_rule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Opciones</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color w-100 @error('color') is-invalid @enderror"
                               id="color" name="color" value="{{ old('color', '#c9a227') }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icono (Font Awesome)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon') }}" placeholder="fas fa-calendar">
                        <div class="form-text">Ej: fas fa-birthday-cake, fas fa-briefcase</div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Guardar Evento
                </button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('is_all_day').addEventListener('change', function() {
    document.querySelector('.time-fields').style.display = this.checked ? 'none' : 'flex';
});

document.getElementById('is_recurring').addEventListener('change', function() {
    document.querySelector('.recurrence-fields').style.display = this.checked ? 'block' : 'none';
});
</script>
@endpush
@endsection
