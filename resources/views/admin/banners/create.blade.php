@extends('admin.layouts.app')

@section('title', 'Nuevo Banner')

@section('page-header')
    <div>
        <h1>Nuevo Banner</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Imagen del Banner</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="image" class="form-label">Seleccionar Imagen <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*" required>
                        <div class="form-text">
                            Formatos aceptados: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB.
                            Dimensiones recomendadas: 1920x600 px.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="image-preview-container" style="display: none;">
                        <label class="form-label">Vista Previa</label>
                        <div class="border rounded p-2 bg-light">
                            <img id="image-preview" src="" alt="Preview" class="img-fluid rounded">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Texto Alternativo (Alt)</label>
                        <input type="text" class="form-control @error('alt_text') is-invalid @enderror"
                               id="alt_text" name="alt_text" value="{{ old('alt_text') }}"
                               placeholder="Descripción de la imagen para accesibilidad">
                        <div class="form-text">Descripción de la imagen para lectores de pantalla y SEO.</div>
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
                                   id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://...">
                            <div class="form-text">Si se proporciona, el banner será clickeable.</div>
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="link_target" class="form-label">Abrir en</label>
                            <select class="form-select @error('link_target') is-invalid @enderror"
                                    id="link_target" name="link_target">
                                <option value="_self" {{ old('link_target', '_self') == '_self' ? 'selected' : '' }}>Misma ventana</option>
                                <option value="_blank" {{ old('link_target') == '_blank' ? 'selected' : '' }}>Nueva ventana</option>
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
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle me-2"></i>Recomendaciones</h6>
                    <ul class="small mb-0">
                        <li>Usa imágenes de alta calidad</li>
                        <li>Mantén un aspecto consistente (16:5)</li>
                        <li>Optimiza el peso de las imágenes</li>
                        <li>Incluye texto alternativo descriptivo</li>
                    </ul>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Subir Banner
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
