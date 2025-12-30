@extends('admin.layouts.app')

@section('title', 'Nueva Noticia')

@section('page-header')
    <div>
        <h1>Nueva Noticia</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Noticias</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.news.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contenido de la Noticia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="3" required maxlength="500">{{ old('content') }}</textarea>
                        <div class="form-text">Máximo 500 caracteres. Este texto aparecerá en el cintillo de noticias.</div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Enlace (opcional)</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url') }}" placeholder="https://...">
                        <div class="form-text">Si se proporciona, el texto será un enlace clickeable.</div>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Vigencia</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="published_at" class="form-label">Mostrar desde</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                   id="published_at" name="published_at" value="{{ old('published_at') }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">Mostrar hasta</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                                   id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                            <div class="form-text">Dejar vacío para mostrar indefinidamente.</div>
                            @error('expires_at')
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
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="priority" name="priority" value="1"
                                {{ old('priority') ? 'checked' : '' }}>
                            <label class="form-check-label" for="priority">
                                <i class="fas fa-star text-warning me-1"></i>Noticia Prioritaria
                            </label>
                        </div>
                        <div class="form-text">Las noticias prioritarias se muestran primero en el cintillo.</div>
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
                    <i class="fas fa-save me-2"></i>Guardar Noticia
                </button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>
@endsection
