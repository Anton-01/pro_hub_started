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
        {{-- Selector de Plantillas Predefinidas --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-magic me-2"></i>Cargar desde Plantilla
                </div>
                <span class="badge bg-info">Opcional</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Selecciona una plantilla para rellenar automáticamente los campos del formulario.
                </p>
                <div class="row">
                    <div class="col-md-5 mb-2 mb-md-0">
                        <select class="form-select form-select-sm" id="templateCategory">
                            <option value="">-- Selecciona categoría --</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-2 mb-md-0">
                        <select class="form-select form-select-sm" id="templateModule" disabled>
                            <option value="">-- Selecciona módulo --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-primary w-100" id="applyTemplateBtn" disabled>
                            <i class="fas fa-check me-1"></i>Aplicar
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        // Color picker sync
        document.getElementById('background_color').addEventListener('input', function() {
            document.getElementById('color_text').value = this.value;
        });

        // Template loader functionality
        const categorySelect = document.getElementById('templateCategory');
        const moduleSelect = document.getElementById('templateModule');
        const applyBtn = document.getElementById('applyTemplateBtn');
        let templatesData = {};

        // Category labels
        const categoryLabels = {
            'erp': 'ERP - Sistemas Empresariales',
            'crm': 'CRM - Gestión de Clientes',
            'hr': 'Recursos Humanos',
            'accounting': 'Contabilidad y Finanzas',
            'project_management': 'Gestión de Proyectos',
            'communication': 'Comunicación'
        };

        // Load templates on page load
        // Function to load templates
        function loadTemplates() {
            if (typeof window.axios === 'undefined') {
                // If axios is not available yet, retry after a short delay
                setTimeout(loadTemplates, 100);
                return;
            }
            window.axios.get('{{ route("admin.modules.defaults.api") }}')
                .then(function(response) {
                    templatesData = response.data;
                    // Populate category dropdown
                    Object.keys(templatesData).forEach(function(category) {
                        const option = document.createElement('option');
                        option.value = category;
                        option.textContent = categoryLabels[category] || category.charAt(0).toUpperCase() + category.slice(1);
                        categorySelect.appendChild(option);
                    });
                })
                .catch(function(error) {
                    console.error('Error loading templates:', error);
                });
        }
        // Load templates
        loadTemplates();

        // Handle category change
        categorySelect.addEventListener('change', function() {
            const category = this.value;
            moduleSelect.innerHTML = '<option value="">-- Selecciona módulo --</option>';
            if (category && templatesData[category]) {
                moduleSelect.disabled = false;
                templatesData[category].forEach(function(module) {
                    const option = document.createElement('option');
                    option.value = module.id;
                    option.textContent = module.label;
                    option.dataset.module = JSON.stringify(module);
                    moduleSelect.appendChild(option);
                });
            } else {
                moduleSelect.disabled = true;
                applyBtn.disabled = true;
            }
        });

        // Handle module selection
        moduleSelect.addEventListener('change', function() {
            applyBtn.disabled = !this.value;
        });

        // Apply template
        applyBtn.addEventListener('click', function() {
            const selectedOption = moduleSelect.options[moduleSelect.selectedIndex];
            if (!selectedOption || !selectedOption.dataset.module) return;
            const module = JSON.parse(selectedOption.dataset.module);
            // Fill form fields
            document.getElementById('label').value = module.label || '';
            document.getElementById('description').value = module.description || '';
            document.getElementById('type').value = module.type || 'external';
            document.getElementById('url').value = module.url || '';
            document.getElementById('target').value = module.target || '_blank';
            document.getElementById('icon').value = module.icon || '';
            document.getElementById('group_name').value = module.group_name || '';
            if (module.background_color) {
                document.getElementById('background_color').value = module.background_color;
                document.getElementById('color_text').value = module.background_color;
            }
            // Show success feedback
            Swal.fire({
                title: '¡Plantilla aplicada!',
                text: 'Los campos han sido rellenados con los datos de "' + module.label + '"',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            // Scroll to form
            document.querySelector('form').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

    });
</script>
@endpush
