@extends('admin.layouts.app')

@section('title', 'Editar Módulo')

@section('page-header')
    <h1>Editar Módulo</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.modules.index') }}">Módulos</a></li>
            <li class="breadcrumb-item active">{{ $module->label }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('admin.modules.update', $module) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-th-large me-2"></i>Información del Módulo
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="label" class="form-label">Nombre del Módulo *</label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $module->label) }}" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Tipo *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="link" {{ old('type', $module->type) == 'link' ? 'selected' : '' }}>Enlace Interno</option>
                                <option value="external" {{ old('type', $module->type) == 'external' ? 'selected' : '' }}>Enlace Externo</option>
                                <option value="modal" {{ old('type', $module->type) == 'modal' ? 'selected' : '' }}>Modal</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" maxlength="500">{{ old('description', $module->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="url" class="form-label">URL *</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $module->url) }}" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="target" class="form-label">Abrir en</label>
                            <select class="form-select @error('target') is-invalid @enderror" id="target" name="target">
                                <option value="_self" {{ old('target', $module->target) == '_self' ? 'selected' : '' }}>Misma ventana</option>
                                <option value="_blank" {{ old('target', $module->target) == '_blank' ? 'selected' : '' }}>Nueva ventana</option>
                            </select>
                            @error('target')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icono (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text" id="iconPreviewSmall">
                                    <i id="selectedIconPreview" class="{{ old('icon', $module->icon) ?: 'fas fa-icons text-muted' }}"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $module->icon) }}" placeholder="fas fa-home" readonly>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                            <small class="text-muted">Haz clic en "Buscar" para seleccionar un icono</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="background_color" class="form-label">Color de Fondo</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', $module->background_color ?? '#4c78dd') }}">
                                <input type="text" class="form-control" value="{{ old('background_color', $module->background_color ?? '#4c78dd') }}" id="color_text" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_name" class="form-label">Grupo</label>
                            <input type="text" class="form-control @error('group_name') is-invalid @enderror" id="group_name" name="group_name" value="{{ old('group_name', $module->group_name) }}">
                            @error('group_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $module->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status', $module->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="highlight" name="highlight" value="1" {{ old('highlight', $module->highlight) ? 'checked' : '' }}>
                        <label class="form-check-label" for="highlight">
                            <strong>Destacar este módulo</strong>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
{{-- Modal de Selector de Iconos --}}
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconPickerModalLabel">
                    <i class="fas fa-icons me-2"></i>Seleccionar Icono
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="iconSearchInput" placeholder="Buscar iconos... (ej: home, user, settings)" autofocus>
                        <button type="button" class="btn btn-outline-secondary" id="clearIconSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted">Escribe para filtrar los iconos disponibles</small>
                </div>
                <div class="mb-3">
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <input type="radio" class="btn-check" name="iconCategory" id="catAll" value="all" checked>
                        <label class="btn btn-outline-primary" for="catAll">Todos</label>
                        <input type="radio" class="btn-check" name="iconCategory" id="catSolid" value="solid">
                        <label class="btn btn-outline-primary" for="catSolid">Solid</label>
                        <input type="radio" class="btn-check" name="iconCategory" id="catRegular" value="regular">
                        <label class="btn btn-outline-primary" for="catRegular">Regular</label>
                        <input type="radio" class="btn-check" name="iconCategory" id="catBrands" value="brands">
                        <label class="btn btn-outline-primary" for="catBrands">Marcas</label>
                    </div>
                </div>
                <div id="iconGrid" class="row g-2" style="max-height: 400px; overflow-y: auto;"></div>
                <div id="noIconsFound" class="text-center py-4 text-muted" style="display: none;">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>No se encontraron iconos</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <span class="text-muted small">Icono seleccionado: </span>
                    <span id="selectedIconName" class="badge bg-primary">Ninguno</span>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmIconBtn" disabled>
                    <i class="fas fa-check me-1"></i>Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    .icon-item {
        cursor: pointer;
        padding: 12px;
        text-align: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .icon-item:hover {
        background-color: #f0f4ff;
        border-color: #6BA3FF;
    }
    .icon-item.selected {
        background-color: #6BA3FF;
        color: white;
        border-color: #4c78dd;
    }
    .icon-item i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 4px;
    }
    .icon-item .icon-name {
        font-size: 0.7rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color picker sync
    document.getElementById('background_color').addEventListener('input', function() {
        document.getElementById('color_text').value = this.value;
    });

    // Icon Picker
    const iconsList = [
        { name: 'home', class: 'fas fa-home', category: 'solid' },
        { name: 'user', class: 'fas fa-user', category: 'solid' },
        { name: 'users', class: 'fas fa-users', category: 'solid' },
        { name: 'cog', class: 'fas fa-cog', category: 'solid' },
        { name: 'cogs', class: 'fas fa-cogs', category: 'solid' },
        { name: 'wrench', class: 'fas fa-wrench', category: 'solid' },
        { name: 'tools', class: 'fas fa-tools', category: 'solid' },
        { name: 'search', class: 'fas fa-search', category: 'solid' },
        { name: 'bell', class: 'fas fa-bell', category: 'solid' },
        { name: 'envelope', class: 'fas fa-envelope', category: 'solid' },
        { name: 'inbox', class: 'fas fa-inbox', category: 'solid' },
        { name: 'paper-plane', class: 'fas fa-paper-plane', category: 'solid' },
        { name: 'comment', class: 'fas fa-comment', category: 'solid' },
        { name: 'comments', class: 'fas fa-comments', category: 'solid' },
        { name: 'phone', class: 'fas fa-phone', category: 'solid' },
        { name: 'calendar', class: 'fas fa-calendar', category: 'solid' },
        { name: 'calendar-alt', class: 'fas fa-calendar-alt', category: 'solid' },
        { name: 'clock', class: 'fas fa-clock', category: 'solid' },
        { name: 'bookmark', class: 'fas fa-bookmark', category: 'solid' },
        { name: 'star', class: 'fas fa-star', category: 'solid' },
        { name: 'heart', class: 'fas fa-heart', category: 'solid' },
        { name: 'check', class: 'fas fa-check', category: 'solid' },
        { name: 'check-circle', class: 'fas fa-check-circle', category: 'solid' },
        { name: 'times', class: 'fas fa-times', category: 'solid' },
        { name: 'plus', class: 'fas fa-plus', category: 'solid' },
        { name: 'minus', class: 'fas fa-minus', category: 'solid' },
        { name: 'info-circle', class: 'fas fa-info-circle', category: 'solid' },
        { name: 'question-circle', class: 'fas fa-question-circle', category: 'solid' },
        { name: 'exclamation-circle', class: 'fas fa-exclamation-circle', category: 'solid' },
        { name: 'lock', class: 'fas fa-lock', category: 'solid' },
        { name: 'unlock', class: 'fas fa-unlock', category: 'solid' },
        { name: 'key', class: 'fas fa-key', category: 'solid' },
        { name: 'shield-alt', class: 'fas fa-shield-alt', category: 'solid' },
        { name: 'eye', class: 'fas fa-eye', category: 'solid' },
        { name: 'edit', class: 'fas fa-edit', category: 'solid' },
        { name: 'trash', class: 'fas fa-trash', category: 'solid' },
        { name: 'copy', class: 'fas fa-copy', category: 'solid' },
        { name: 'clipboard', class: 'fas fa-clipboard', category: 'solid' },
        { name: 'save', class: 'fas fa-save', category: 'solid' },
        { name: 'download', class: 'fas fa-download', category: 'solid' },
        { name: 'upload', class: 'fas fa-upload', category: 'solid' },
        { name: 'cloud', class: 'fas fa-cloud', category: 'solid' },
        { name: 'sync', class: 'fas fa-sync', category: 'solid' },
        { name: 'link', class: 'fas fa-link', category: 'solid' },
        { name: 'external-link-alt', class: 'fas fa-external-link-alt', category: 'solid' },
        { name: 'share', class: 'fas fa-share', category: 'solid' },
        { name: 'print', class: 'fas fa-print', category: 'solid' },
        { name: 'filter', class: 'fas fa-filter', category: 'solid' },
        { name: 'sort', class: 'fas fa-sort', category: 'solid' },
        { name: 'list', class: 'fas fa-list', category: 'solid' },
        { name: 'th', class: 'fas fa-th', category: 'solid' },
        { name: 'table', class: 'fas fa-table', category: 'solid' },
        { name: 'bars', class: 'fas fa-bars', category: 'solid' },
        { name: 'file', class: 'fas fa-file', category: 'solid' },
        { name: 'file-alt', class: 'fas fa-file-alt', category: 'solid' },
        { name: 'file-pdf', class: 'fas fa-file-pdf', category: 'solid' },
        { name: 'file-excel', class: 'fas fa-file-excel', category: 'solid' },
        { name: 'folder', class: 'fas fa-folder', category: 'solid' },
        { name: 'folder-open', class: 'fas fa-folder-open', category: 'solid' },
        { name: 'briefcase', class: 'fas fa-briefcase', category: 'solid' },
        { name: 'building', class: 'fas fa-building', category: 'solid' },
        { name: 'store', class: 'fas fa-store', category: 'solid' },
        { name: 'industry', class: 'fas fa-industry', category: 'solid' },
        { name: 'money-bill', class: 'fas fa-money-bill', category: 'solid' },
        { name: 'credit-card', class: 'fas fa-credit-card', category: 'solid' },
        { name: 'wallet', class: 'fas fa-wallet', category: 'solid' },
        { name: 'chart-line', class: 'fas fa-chart-line', category: 'solid' },
        { name: 'chart-bar', class: 'fas fa-chart-bar', category: 'solid' },
        { name: 'chart-pie', class: 'fas fa-chart-pie', category: 'solid' },
        { name: 'calculator', class: 'fas fa-calculator', category: 'solid' },
        { name: 'laptop', class: 'fas fa-laptop', category: 'solid' },
        { name: 'desktop', class: 'fas fa-desktop', category: 'solid' },
        { name: 'server', class: 'fas fa-server', category: 'solid' },
        { name: 'database', class: 'fas fa-database', category: 'solid' },
        { name: 'code', class: 'fas fa-code', category: 'solid' },
        { name: 'globe', class: 'fas fa-globe', category: 'solid' },
        { name: 'map-marker-alt', class: 'fas fa-map-marker-alt', category: 'solid' },
        { name: 'car', class: 'fas fa-car', category: 'solid' },
        { name: 'truck', class: 'fas fa-truck', category: 'solid' },
        { name: 'plane', class: 'fas fa-plane', category: 'solid' },
        { name: 'graduation-cap', class: 'fas fa-graduation-cap', category: 'solid' },
        { name: 'book', class: 'fas fa-book', category: 'solid' },
        { name: 'hospital', class: 'fas fa-hospital', category: 'solid' },
        { name: 'medkit', class: 'fas fa-medkit', category: 'solid' },
        { name: 'shopping-cart', class: 'fas fa-shopping-cart', category: 'solid' },
        { name: 'tag', class: 'fas fa-tag', category: 'solid' },
        { name: 'box', class: 'fas fa-box', category: 'solid' },
        { name: 'gift', class: 'fas fa-gift', category: 'solid' },
        // Regular icons
        { name: 'file (regular)', class: 'far fa-file', category: 'regular' },
        { name: 'folder (regular)', class: 'far fa-folder', category: 'regular' },
        { name: 'envelope (regular)', class: 'far fa-envelope', category: 'regular' },
        { name: 'bell (regular)', class: 'far fa-bell', category: 'regular' },
        { name: 'calendar (regular)', class: 'far fa-calendar', category: 'regular' },
        { name: 'clock (regular)', class: 'far fa-clock', category: 'regular' },
        { name: 'heart (regular)', class: 'far fa-heart', category: 'regular' },
        { name: 'star (regular)', class: 'far fa-star', category: 'regular' },
        { name: 'user (regular)', class: 'far fa-user', category: 'regular' },
        { name: 'comment (regular)', class: 'far fa-comment', category: 'regular' },
        { name: 'edit (regular)', class: 'far fa-edit', category: 'regular' },
        { name: 'copy (regular)', class: 'far fa-copy', category: 'regular' },
        { name: 'clipboard (regular)', class: 'far fa-clipboard', category: 'regular' },
        { name: 'image (regular)', class: 'far fa-image', category: 'regular' },
        { name: 'building (regular)', class: 'far fa-building', category: 'regular' },
        // Brands
        { name: 'google', class: 'fab fa-google', category: 'brands' },
        { name: 'microsoft', class: 'fab fa-microsoft', category: 'brands' },
        { name: 'apple', class: 'fab fa-apple', category: 'brands' },
        { name: 'github', class: 'fab fa-github', category: 'brands' },
        { name: 'slack', class: 'fab fa-slack', category: 'brands' },
        { name: 'trello', class: 'fab fa-trello', category: 'brands' },
        { name: 'dropbox', class: 'fab fa-dropbox', category: 'brands' },
        { name: 'facebook', class: 'fab fa-facebook', category: 'brands' },
        { name: 'instagram', class: 'fab fa-instagram', category: 'brands' },
        { name: 'twitter', class: 'fab fa-twitter', category: 'brands' },
        { name: 'linkedin', class: 'fab fa-linkedin', category: 'brands' },
        { name: 'youtube', class: 'fab fa-youtube', category: 'brands' },
        { name: 'whatsapp', class: 'fab fa-whatsapp', category: 'brands' },
        { name: 'paypal', class: 'fab fa-paypal', category: 'brands' },
        { name: 'stripe', class: 'fab fa-stripe', category: 'brands' },
        { name: 'laravel', class: 'fab fa-laravel', category: 'brands' },
        { name: 'php', class: 'fab fa-php', category: 'brands' },
        { name: 'react', class: 'fab fa-react', category: 'brands' },
        { name: 'vuejs', class: 'fab fa-vuejs', category: 'brands' },
        { name: 'aws', class: 'fab fa-aws', category: 'brands' },
        { name: 'docker', class: 'fab fa-docker', category: 'brands' },
        { name: 'salesforce', class: 'fab fa-salesforce', category: 'brands' },
        { name: 'hubspot', class: 'fab fa-hubspot', category: 'brands' },
    ];

    let selectedIcon = null;
    const iconSearchInput = document.getElementById('iconSearchInput');
    const iconGrid = document.getElementById('iconGrid');
    const noIconsFound = document.getElementById('noIconsFound');
    const confirmIconBtn = document.getElementById('confirmIconBtn');
    const selectedIconName = document.getElementById('selectedIconName');
    const clearIconSearch = document.getElementById('clearIconSearch');
    const iconInput = document.getElementById('icon');
    const selectedIconPreview = document.getElementById('selectedIconPreview');

    function renderIcons(filter = '', category = 'all') {
        const filteredIcons = iconsList.filter(icon => {
            const matchesSearch = icon.name.toLowerCase().includes(filter.toLowerCase()) ||
                                 icon.class.toLowerCase().includes(filter.toLowerCase());
            const matchesCategory = category === 'all' || icon.category === category;
            return matchesSearch && matchesCategory;
        });

        if (filteredIcons.length === 0) {
            iconGrid.innerHTML = '';
            noIconsFound.style.display = 'block';
            return;
        }

        noIconsFound.style.display = 'none';
        iconGrid.innerHTML = filteredIcons.map(icon => `
            <div class="col-4 col-sm-3 col-md-2">
                <div class="icon-item ${selectedIcon === icon.class ? 'selected' : ''}"
                     data-icon="${icon.class}" data-name="${icon.name}">
                    <i class="${icon.class}"></i>
                    <div class="icon-name">${icon.name}</div>
                </div>
            </div>
        `).join('');

        document.querySelectorAll('.icon-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.icon-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                selectedIcon = this.dataset.icon;
                selectedIconName.textContent = this.dataset.name;
                confirmIconBtn.disabled = false;
            });
        });
    }

    document.getElementById('iconPickerModal').addEventListener('shown.bs.modal', function() {
        renderIcons();
        iconSearchInput.focus();
    });

    iconSearchInput.addEventListener('input', function(e) {
        e.stopPropagation();
        const category = document.querySelector('input[name="iconCategory"]:checked').value;
        renderIcons(this.value, category);
    });

    iconSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    clearIconSearch.addEventListener('click', function() {
        iconSearchInput.value = '';
        const category = document.querySelector('input[name="iconCategory"]:checked').value;
        renderIcons('', category);
        iconSearchInput.focus();
    });

    document.querySelectorAll('input[name="iconCategory"]').forEach(radio => {
        radio.addEventListener('change', function() {
            renderIcons(iconSearchInput.value, this.value);
        });
    });

    confirmIconBtn.addEventListener('click', function() {
        if (selectedIcon) {
            iconInput.value = selectedIcon;
            selectedIconPreview.className = selectedIcon;
            bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
            selectedIcon = null;
            selectedIconName.textContent = 'Ninguno';
            confirmIconBtn.disabled = true;
            iconSearchInput.value = '';
        }
    });
});
</script>
@endpush
