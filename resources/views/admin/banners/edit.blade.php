@extends('admin.layouts.app')

@section('title', 'Editar Banner')

@section('page-header')
    <div>
        <h1>Editar Banner</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Imagen del Banner</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Imagen Actual</label>
                        <div class="border rounded p-2 bg-light">
                            <img src="{{ Storage::url($banner->url) }}" alt="{{ $banner->alt_text }}" class="img-fluid rounded">
                        </div>
                        <div class="small text-muted mt-2">
                            {{ $banner->original_name }} &bull; {{ $banner->dimensions }} &bull; {{ $banner->formatted_file_size }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">Reemplazar Imagen (opcional)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        <div class="form-text">
                            Solo sube una nueva imagen si deseas reemplazar la actual.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="image-preview-container" style="display: none;">
                        <label class="form-label">Nueva Vista Previa</label>
                        <div class="border rounded p-2 bg-light">
                            <img id="image-preview" src="" alt="Preview" class="img-fluid rounded">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Texto Alternativo (Alt)</label>
                        <input type="text" class="form-control @error('alt_text') is-invalid @enderror"
                               id="alt_text" name="alt_text" value="{{ old('alt_text', $banner->alt_text) }}"
                               placeholder="Descripción de la imagen para accesibilidad">
                        @error('alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Enlace (Opcional)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="link_url" class="form-label">URL del Enlace</label>
                            <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                                   id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://...">
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="link_target" class="form-label">Abrir en</label>
                            <select class="form-select @error('link_target') is-invalid @enderror"
                                    id="link_target" name="link_target">
                                <option value="_self" {{ old('link_target', $banner->link_target) == '_self' ? 'selected' : '' }}>Misma ventana</option>
                                <option value="_blank" {{ old('link_target', $banner->link_target) == '_blank' ? 'selected' : '' }}>Nueva ventana</option>
                            </select>
                            @error('link_target')
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
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $banner->status) == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $banner->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body small text-muted">
                    <div><strong>Creado:</strong> {{ $banner->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Actualizado:</strong> {{ $banner->updated_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Orden:</strong> #{{ $banner->sort_order }}</div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Actualizar Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('image-preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
