@extends('admin.layouts.app')

@section('title', 'Editar Noticia')

@section('page-header')
    <div>
        <h1>Editar Noticia</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Noticias</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.news.update', $news) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contenido de la Noticia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="text" class="form-label">Texto <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('text') is-invalid @enderror"
                                  id="text" name="text" rows="3" required maxlength="500">{{ old('text', $news->text) }}</textarea>
                        <div class="form-text">Máximo 500 caracteres. Este texto aparecerá en el cintillo de noticias.</div>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Enlace (opcional)</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url', $news->url) }}" placeholder="https://...">
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
                            <label for="starts_at" class="form-label">Mostrar desde</label>
                            <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
                                   id="starts_at" name="starts_at" value="{{ old('starts_at', $news->starts_at ? $news->starts_at->format('Y-m-d\TH:i') : '') }}">
                            <div class="form-text">Dejar vacío para mostrar inmediatamente.</div>
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ends_at" class="form-label">Mostrar hasta</label>
                            <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror"
                                   id="ends_at" name="ends_at" value="{{ old('ends_at', $news->ends_at ? $news->ends_at->format('Y-m-d\TH:i') : '') }}">
                            <div class="form-text">Dejar vacío para mostrar indefinidamente.</div>
                            @error('ends_at')
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
                            <input type="checkbox" class="form-check-input" id="is_priority" name="is_priority" value="1"
                                   {{ old('is_priority', $news->is_priority) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_priority">
                                <i class="fas fa-star text-warning me-1"></i>Noticia Prioritaria
                            </label>
                        </div>
                        <div class="form-text">Las noticias prioritarias se muestran primero en el cintillo.</div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $news->status) == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $news->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body small text-muted">
                    <div><strong>Creado:</strong> {{ $news->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Actualizado:</strong> {{ $news->updated_at->format('d/m/Y H:i') }}</div>
                    @if($news->creator)
                        <div><strong>Creado por:</strong> {{ $news->creator->name }}</div>
                    @endif
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Actualizar Noticia
                </button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>
@endsection
