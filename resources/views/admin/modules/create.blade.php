@extends('admin.layouts.app')

@section('title', 'Nuevo Módulo')

@section('page-header')
    <h1>Nuevo Módulo</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.modules.index') }}">Módulos</a></li>
            <li class="breadcrumb-item active">Nuevo</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('admin.modules.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-th-large me-2"></i>Información del Módulo
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="label" class="form-label">Nombre del Módulo *</label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label') }}" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Tipo *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>Enlace Interno</option>
                                <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>Enlace Externo</option>
                                <option value="modal" {{ old('type') == 'modal' ? 'selected' : '' }}>Modal</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" maxlength="500">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="url" class="form-label">URL *</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" placeholder="https:// o ruta interna" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="target" class="form-label">Abrir en</label>
                            <select class="form-select @error('target') is-invalid @enderror" id="target" name="target">
                                <option value="_self" {{ old('target') == '_self' ? 'selected' : '' }}>Misma ventana</option>
                                <option value="_blank" {{ old('target') == '_blank' ? 'selected' : '' }}>Nueva ventana</option>
                            </select>
                            @error('target')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icono (FontAwesome)</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="fas fa-home">
                            <small class="text-muted">Ej: fas fa-home, far fa-file, fab fa-google</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="background_color" class="form-label">Color de Fondo</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', '#4c78dd') }}">
                                <input type="text" class="form-control" value="{{ old('background_color', '#4c78dd') }}" id="color_text" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_name" class="form-label">Grupo</label>
                            <input type="text" class="form-control @error('group_name') is-invalid @enderror" id="group_name" name="group_name" value="{{ old('group_name') }}" placeholder="Nombre del grupo">
                            @error('group_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="highlight" name="highlight" value="1" {{ old('highlight') ? 'checked' : '' }}>
                        <label class="form-check-label" for="highlight">
                            <strong>Destacar este módulo</strong>
                            <small class="text-muted d-block">Se mostrará de forma prominente en el portal</small>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Módulo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('background_color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});
</script>
@endpush
