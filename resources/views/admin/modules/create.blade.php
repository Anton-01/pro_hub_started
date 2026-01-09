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
                            <div class="input-group">
                                <span class="input-group-text" id="iconPreviewSmall">
                                    <i id="selectedIconPreview" class="fas fa-icons text-muted"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="fas fa-home" readonly>
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
                <div id="iconGrid" class="row g-2" style="max-height: 400px; overflow-y: auto;">
                    {{-- Los iconos se cargarán dinámicamente --}}
                </div>
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
    #iconGrid::-webkit-scrollbar {
        width: 8px;
    }
    #iconGrid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    #iconGrid::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    #iconGrid::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
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

        // ========================================
        // Icon Picker Functionality
        // ========================================
        const iconsList = [
            // General / Interface
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
            { name: 'mobile', class: 'fas fa-mobile-alt', category: 'solid' },
            { name: 'calendar', class: 'fas fa-calendar', category: 'solid' },
            { name: 'calendar-alt', class: 'fas fa-calendar-alt', category: 'solid' },
            { name: 'clock', class: 'fas fa-clock', category: 'solid' },
            { name: 'history', class: 'fas fa-history', category: 'solid' },
            { name: 'bookmark', class: 'fas fa-bookmark', category: 'solid' },
            { name: 'star', class: 'fas fa-star', category: 'solid' },
            { name: 'heart', class: 'fas fa-heart', category: 'solid' },
            { name: 'thumbs-up', class: 'fas fa-thumbs-up', category: 'solid' },
            { name: 'thumbs-down', class: 'fas fa-thumbs-down', category: 'solid' },
            { name: 'check', class: 'fas fa-check', category: 'solid' },
            { name: 'check-circle', class: 'fas fa-check-circle', category: 'solid' },
            { name: 'times', class: 'fas fa-times', category: 'solid' },
            { name: 'times-circle', class: 'fas fa-times-circle', category: 'solid' },
            { name: 'plus', class: 'fas fa-plus', category: 'solid' },
            { name: 'plus-circle', class: 'fas fa-plus-circle', category: 'solid' },
            { name: 'minus', class: 'fas fa-minus', category: 'solid' },
            { name: 'minus-circle', class: 'fas fa-minus-circle', category: 'solid' },
            { name: 'info', class: 'fas fa-info', category: 'solid' },
            { name: 'info-circle', class: 'fas fa-info-circle', category: 'solid' },
            { name: 'question', class: 'fas fa-question', category: 'solid' },
            { name: 'question-circle', class: 'fas fa-question-circle', category: 'solid' },
            { name: 'exclamation', class: 'fas fa-exclamation', category: 'solid' },
            { name: 'exclamation-circle', class: 'fas fa-exclamation-circle', category: 'solid' },
            { name: 'exclamation-triangle', class: 'fas fa-exclamation-triangle', category: 'solid' },
            { name: 'lock', class: 'fas fa-lock', category: 'solid' },
            { name: 'unlock', class: 'fas fa-unlock', category: 'solid' },
            { name: 'key', class: 'fas fa-key', category: 'solid' },
            { name: 'shield-alt', class: 'fas fa-shield-alt', category: 'solid' },
            { name: 'eye', class: 'fas fa-eye', category: 'solid' },
            { name: 'eye-slash', class: 'fas fa-eye-slash', category: 'solid' },
            { name: 'edit', class: 'fas fa-edit', category: 'solid' },
            { name: 'pen', class: 'fas fa-pen', category: 'solid' },
            { name: 'pencil-alt', class: 'fas fa-pencil-alt', category: 'solid' },
            { name: 'trash', class: 'fas fa-trash', category: 'solid' },
            { name: 'trash-alt', class: 'fas fa-trash-alt', category: 'solid' },
            { name: 'copy', class: 'fas fa-copy', category: 'solid' },
            { name: 'paste', class: 'fas fa-paste', category: 'solid' },
            { name: 'clipboard', class: 'fas fa-clipboard', category: 'solid' },
            { name: 'clipboard-list', class: 'fas fa-clipboard-list', category: 'solid' },
            { name: 'clipboard-check', class: 'fas fa-clipboard-check', category: 'solid' },
            { name: 'save', class: 'fas fa-save', category: 'solid' },
            { name: 'download', class: 'fas fa-download', category: 'solid' },
            { name: 'upload', class: 'fas fa-upload', category: 'solid' },
            { name: 'cloud', class: 'fas fa-cloud', category: 'solid' },
            { name: 'cloud-upload-alt', class: 'fas fa-cloud-upload-alt', category: 'solid' },
            { name: 'cloud-download-alt', class: 'fas fa-cloud-download-alt', category: 'solid' },
            { name: 'sync', class: 'fas fa-sync', category: 'solid' },
            { name: 'sync-alt', class: 'fas fa-sync-alt', category: 'solid' },
            { name: 'redo', class: 'fas fa-redo', category: 'solid' },
            { name: 'undo', class: 'fas fa-undo', category: 'solid' },
            { name: 'refresh', class: 'fas fa-redo-alt', category: 'solid' },
            { name: 'link', class: 'fas fa-link', category: 'solid' },
            { name: 'unlink', class: 'fas fa-unlink', category: 'solid' },
            { name: 'external-link-alt', class: 'fas fa-external-link-alt', category: 'solid' },
            { name: 'share', class: 'fas fa-share', category: 'solid' },
            { name: 'share-alt', class: 'fas fa-share-alt', category: 'solid' },
            { name: 'share-square', class: 'fas fa-share-square', category: 'solid' },
            { name: 'print', class: 'fas fa-print', category: 'solid' },
            { name: 'qrcode', class: 'fas fa-qrcode', category: 'solid' },
            { name: 'barcode', class: 'fas fa-barcode', category: 'solid' },
            { name: 'filter', class: 'fas fa-filter', category: 'solid' },
            { name: 'sort', class: 'fas fa-sort', category: 'solid' },
            { name: 'sort-up', class: 'fas fa-sort-up', category: 'solid' },
            { name: 'sort-down', class: 'fas fa-sort-down', category: 'solid' },
            { name: 'list', class: 'fas fa-list', category: 'solid' },
            { name: 'list-ul', class: 'fas fa-list-ul', category: 'solid' },
            { name: 'list-ol', class: 'fas fa-list-ol', category: 'solid' },
            { name: 'th', class: 'fas fa-th', category: 'solid' },
            { name: 'th-large', class: 'fas fa-th-large', category: 'solid' },
            { name: 'th-list', class: 'fas fa-th-list', category: 'solid' },
            { name: 'table', class: 'fas fa-table', category: 'solid' },
            { name: 'columns', class: 'fas fa-columns', category: 'solid' },
            { name: 'bars', class: 'fas fa-bars', category: 'solid' },
            { name: 'ellipsis-h', class: 'fas fa-ellipsis-h', category: 'solid' },
            { name: 'ellipsis-v', class: 'fas fa-ellipsis-v', category: 'solid' },
            { name: 'grip-horizontal', class: 'fas fa-grip-horizontal', category: 'solid' },
            { name: 'grip-vertical', class: 'fas fa-grip-vertical', category: 'solid' },
            // Files & Documents
            { name: 'file', class: 'fas fa-file', category: 'solid' },
            { name: 'file-alt', class: 'fas fa-file-alt', category: 'solid' },
            { name: 'file-pdf', class: 'fas fa-file-pdf', category: 'solid' },
            { name: 'file-word', class: 'fas fa-file-word', category: 'solid' },
            { name: 'file-excel', class: 'fas fa-file-excel', category: 'solid' },
            { name: 'file-powerpoint', class: 'fas fa-file-powerpoint', category: 'solid' },
            { name: 'file-image', class: 'fas fa-file-image', category: 'solid' },
            { name: 'file-video', class: 'fas fa-file-video', category: 'solid' },
            { name: 'file-audio', class: 'fas fa-file-audio', category: 'solid' },
            { name: 'file-archive', class: 'fas fa-file-archive', category: 'solid' },
            { name: 'file-code', class: 'fas fa-file-code', category: 'solid' },
            { name: 'file-csv', class: 'fas fa-file-csv', category: 'solid' },
            { name: 'file-invoice', class: 'fas fa-file-invoice', category: 'solid' },
            { name: 'file-invoice-dollar', class: 'fas fa-file-invoice-dollar', category: 'solid' },
            { name: 'file-contract', class: 'fas fa-file-contract', category: 'solid' },
            { name: 'file-signature', class: 'fas fa-file-signature', category: 'solid' },
            { name: 'file-medical', class: 'fas fa-file-medical', category: 'solid' },
            { name: 'file-medical-alt', class: 'fas fa-file-medical-alt', category: 'solid' },
            { name: 'file-download', class: 'fas fa-file-download', category: 'solid' },
            { name: 'file-upload', class: 'fas fa-file-upload', category: 'solid' },
            { name: 'file-import', class: 'fas fa-file-import', category: 'solid' },
            { name: 'file-export', class: 'fas fa-file-export', category: 'solid' },
            { name: 'folder', class: 'fas fa-folder', category: 'solid' },
            { name: 'folder-open', class: 'fas fa-folder-open', category: 'solid' },
            { name: 'folder-plus', class: 'fas fa-folder-plus', category: 'solid' },
            { name: 'folder-minus', class: 'fas fa-folder-minus', category: 'solid' },
            // Business & Finance
            { name: 'briefcase', class: 'fas fa-briefcase', category: 'solid' },
            { name: 'building', class: 'fas fa-building', category: 'solid' },
            { name: 'city', class: 'fas fa-city', category: 'solid' },
            { name: 'store', class: 'fas fa-store', category: 'solid' },
            { name: 'store-alt', class: 'fas fa-store-alt', category: 'solid' },
            { name: 'industry', class: 'fas fa-industry', category: 'solid' },
            { name: 'warehouse', class: 'fas fa-warehouse', category: 'solid' },
            { name: 'landmark', class: 'fas fa-landmark', category: 'solid' },
            { name: 'university', class: 'fas fa-university', category: 'solid' },
            { name: 'money-bill', class: 'fas fa-money-bill', category: 'solid' },
            { name: 'money-bill-alt', class: 'fas fa-money-bill-alt', category: 'solid' },
            { name: 'money-bill-wave', class: 'fas fa-money-bill-wave', category: 'solid' },
            { name: 'money-check', class: 'fas fa-money-check', category: 'solid' },
            { name: 'money-check-alt', class: 'fas fa-money-check-alt', category: 'solid' },
            { name: 'credit-card', class: 'fas fa-credit-card', category: 'solid' },
            { name: 'wallet', class: 'fas fa-wallet', category: 'solid' },
            { name: 'piggy-bank', class: 'fas fa-piggy-bank', category: 'solid' },
            { name: 'coins', class: 'fas fa-coins', category: 'solid' },
            { name: 'dollar-sign', class: 'fas fa-dollar-sign', category: 'solid' },
            { name: 'euro-sign', class: 'fas fa-euro-sign', category: 'solid' },
            { name: 'pound-sign', class: 'fas fa-pound-sign', category: 'solid' },
            { name: 'yen-sign', class: 'fas fa-yen-sign', category: 'solid' },
            { name: 'receipt', class: 'fas fa-receipt', category: 'solid' },
            { name: 'calculator', class: 'fas fa-calculator', category: 'solid' },
            { name: 'chart-line', class: 'fas fa-chart-line', category: 'solid' },
            { name: 'chart-bar', class: 'fas fa-chart-bar', category: 'solid' },
            { name: 'chart-pie', class: 'fas fa-chart-pie', category: 'solid' },
            { name: 'chart-area', class: 'fas fa-chart-area', category: 'solid' },
            { name: 'percentage', class: 'fas fa-percentage', category: 'solid' },
            { name: 'balance-scale', class: 'fas fa-balance-scale', category: 'solid' },
            { name: 'balance-scale-left', class: 'fas fa-balance-scale-left', category: 'solid' },
            { name: 'balance-scale-right', class: 'fas fa-balance-scale-right', category: 'solid' },
            { name: 'handshake', class: 'fas fa-handshake', category: 'solid' },
            { name: 'hands-helping', class: 'fas fa-hands-helping', category: 'solid' },
            { name: 'address-book', class: 'fas fa-address-book', category: 'solid' },
            { name: 'address-card', class: 'fas fa-address-card', category: 'solid' },
            { name: 'id-badge', class: 'fas fa-id-badge', category: 'solid' },
            { name: 'id-card', class: 'fas fa-id-card', category: 'solid' },
            { name: 'id-card-alt', class: 'fas fa-id-card-alt', category: 'solid' },
            { name: 'user-tie', class: 'fas fa-user-tie', category: 'solid' },
            { name: 'user-cog', class: 'fas fa-user-cog', category: 'solid' },
            { name: 'user-edit', class: 'fas fa-user-edit', category: 'solid' },
            { name: 'user-plus', class: 'fas fa-user-plus', category: 'solid' },
            { name: 'user-minus', class: 'fas fa-user-minus', category: 'solid' },
            { name: 'user-check', class: 'fas fa-user-check', category: 'solid' },
            { name: 'user-times', class: 'fas fa-user-times', category: 'solid' },
            { name: 'user-lock', class: 'fas fa-user-lock', category: 'solid' },
            { name: 'user-shield', class: 'fas fa-user-shield', category: 'solid' },
            { name: 'user-tag', class: 'fas fa-user-tag', category: 'solid' },
            { name: 'user-clock', class: 'fas fa-user-clock', category: 'solid' },
            { name: 'user-friends', class: 'fas fa-user-friends', category: 'solid' },
            { name: 'users-cog', class: 'fas fa-users-cog', category: 'solid' },
            { name: 'people-arrows', class: 'fas fa-people-arrows', category: 'solid' },
            { name: 'sitemap', class: 'fas fa-sitemap', category: 'solid' },
            { name: 'network-wired', class: 'fas fa-network-wired', category: 'solid' },
            { name: 'project-diagram', class: 'fas fa-project-diagram', category: 'solid' },
            // Technology
            { name: 'laptop', class: 'fas fa-laptop', category: 'solid' },
            { name: 'laptop-code', class: 'fas fa-laptop-code', category: 'solid' },
            { name: 'desktop', class: 'fas fa-desktop', category: 'solid' },
            { name: 'tablet', class: 'fas fa-tablet', category: 'solid' },
            { name: 'tablet-alt', class: 'fas fa-tablet-alt', category: 'solid' },
            { name: 'server', class: 'fas fa-server', category: 'solid' },
            { name: 'database', class: 'fas fa-database', category: 'solid' },
            { name: 'hdd', class: 'fas fa-hdd', category: 'solid' },
            { name: 'microchip', class: 'fas fa-microchip', category: 'solid' },
            { name: 'memory', class: 'fas fa-memory', category: 'solid' },
            { name: 'keyboard', class: 'fas fa-keyboard', category: 'solid' },
            { name: 'mouse', class: 'fas fa-mouse', category: 'solid' },
            { name: 'headphones', class: 'fas fa-headphones', category: 'solid' },
            { name: 'headset', class: 'fas fa-headset', category: 'solid' },
            { name: 'microphone', class: 'fas fa-microphone', category: 'solid' },
            { name: 'microphone-alt', class: 'fas fa-microphone-alt', category: 'solid' },
            { name: 'camera', class: 'fas fa-camera', category: 'solid' },
            { name: 'video', class: 'fas fa-video', category: 'solid' },
            { name: 'tv', class: 'fas fa-tv', category: 'solid' },
            { name: 'wifi', class: 'fas fa-wifi', category: 'solid' },
            { name: 'bluetooth', class: 'fab fa-bluetooth', category: 'brands' },
            { name: 'signal', class: 'fas fa-signal', category: 'solid' },
            { name: 'satellite', class: 'fas fa-satellite', category: 'solid' },
            { name: 'satellite-dish', class: 'fas fa-satellite-dish', category: 'solid' },
            { name: 'plug', class: 'fas fa-plug', category: 'solid' },
            { name: 'battery-full', class: 'fas fa-battery-full', category: 'solid' },
            { name: 'battery-half', class: 'fas fa-battery-half', category: 'solid' },
            { name: 'battery-quarter', class: 'fas fa-battery-quarter', category: 'solid' },
            { name: 'battery-empty', class: 'fas fa-battery-empty', category: 'solid' },
            { name: 'code', class: 'fas fa-code', category: 'solid' },
            { name: 'code-branch', class: 'fas fa-code-branch', category: 'solid' },
            { name: 'terminal', class: 'fas fa-terminal', category: 'solid' },
            { name: 'bug', class: 'fas fa-bug', category: 'solid' },
            { name: 'robot', class: 'fas fa-robot', category: 'solid' },
            { name: 'globe', class: 'fas fa-globe', category: 'solid' },
            { name: 'globe-americas', class: 'fas fa-globe-americas', category: 'solid' },
            { name: 'globe-europe', class: 'fas fa-globe-europe', category: 'solid' },
            { name: 'globe-asia', class: 'fas fa-globe-asia', category: 'solid' },
            { name: 'globe-africa', class: 'fas fa-globe-africa', category: 'solid' },
            { name: 'atlas', class: 'fas fa-atlas', category: 'solid' },
            { name: 'map', class: 'fas fa-map', category: 'solid' },
            { name: 'map-marked', class: 'fas fa-map-marked', category: 'solid' },
            { name: 'map-marked-alt', class: 'fas fa-map-marked-alt', category: 'solid' },
            { name: 'map-marker', class: 'fas fa-map-marker', category: 'solid' },
            { name: 'map-marker-alt', class: 'fas fa-map-marker-alt', category: 'solid' },
            { name: 'location-arrow', class: 'fas fa-location-arrow', category: 'solid' },
            { name: 'compass', class: 'fas fa-compass', category: 'solid' },
            // Transportation
            { name: 'car', class: 'fas fa-car', category: 'solid' },
            { name: 'car-alt', class: 'fas fa-car-alt', category: 'solid' },
            { name: 'car-side', class: 'fas fa-car-side', category: 'solid' },
            { name: 'taxi', class: 'fas fa-taxi', category: 'solid' },
            { name: 'bus', class: 'fas fa-bus', category: 'solid' },
            { name: 'bus-alt', class: 'fas fa-bus-alt', category: 'solid' },
            { name: 'truck', class: 'fas fa-truck', category: 'solid' },
            { name: 'truck-loading', class: 'fas fa-truck-loading', category: 'solid' },
            { name: 'truck-moving', class: 'fas fa-truck-moving', category: 'solid' },
            { name: 'shipping-fast', class: 'fas fa-shipping-fast', category: 'solid' },
            { name: 'motorcycle', class: 'fas fa-motorcycle', category: 'solid' },
            { name: 'bicycle', class: 'fas fa-bicycle', category: 'solid' },
            { name: 'plane', class: 'fas fa-plane', category: 'solid' },
            { name: 'plane-departure', class: 'fas fa-plane-departure', category: 'solid' },
            { name: 'plane-arrival', class: 'fas fa-plane-arrival', category: 'solid' },
            { name: 'helicopter', class: 'fas fa-helicopter', category: 'solid' },
            { name: 'ship', class: 'fas fa-ship', category: 'solid' },
            { name: 'anchor', class: 'fas fa-anchor', category: 'solid' },
            { name: 'subway', class: 'fas fa-subway', category: 'solid' },
            { name: 'train', class: 'fas fa-train', category: 'solid' },
            { name: 'tram', class: 'fas fa-tram', category: 'solid' },
            { name: 'shuttle-van', class: 'fas fa-shuttle-van', category: 'solid' },
            // Education & Learning
            { name: 'graduation-cap', class: 'fas fa-graduation-cap', category: 'solid' },
            { name: 'book', class: 'fas fa-book', category: 'solid' },
            { name: 'book-open', class: 'fas fa-book-open', category: 'solid' },
            { name: 'book-reader', class: 'fas fa-book-reader', category: 'solid' },
            { name: 'bookmark', class: 'fas fa-bookmark', category: 'solid' },
            { name: 'chalkboard', class: 'fas fa-chalkboard', category: 'solid' },
            { name: 'chalkboard-teacher', class: 'fas fa-chalkboard-teacher', category: 'solid' },
            { name: 'school', class: 'fas fa-school', category: 'solid' },
            { name: 'apple-alt', class: 'fas fa-apple-alt', category: 'solid' },
            { name: 'atom', class: 'fas fa-atom', category: 'solid' },
            { name: 'brain', class: 'fas fa-brain', category: 'solid' },
            { name: 'lightbulb', class: 'fas fa-lightbulb', category: 'solid' },
            { name: 'microscope', class: 'fas fa-microscope', category: 'solid' },
            { name: 'flask', class: 'fas fa-flask', category: 'solid' },
            { name: 'vial', class: 'fas fa-vial', category: 'solid' },
            { name: 'vials', class: 'fas fa-vials', category: 'solid' },
            { name: 'dna', class: 'fas fa-dna', category: 'solid' },
            { name: 'award', class: 'fas fa-award', category: 'solid' },
            { name: 'medal', class: 'fas fa-medal', category: 'solid' },
            { name: 'trophy', class: 'fas fa-trophy', category: 'solid' },
            { name: 'certificate', class: 'fas fa-certificate', category: 'solid' },
            // Healthcare & Medical
            { name: 'heart', class: 'fas fa-heart', category: 'solid' },
            { name: 'heartbeat', class: 'fas fa-heartbeat', category: 'solid' },
            { name: 'hospital', class: 'fas fa-hospital', category: 'solid' },
            { name: 'hospital-alt', class: 'fas fa-hospital-alt', category: 'solid' },
            { name: 'clinic-medical', class: 'fas fa-clinic-medical', category: 'solid' },
            { name: 'ambulance', class: 'fas fa-ambulance', category: 'solid' },
            { name: 'medkit', class: 'fas fa-medkit', category: 'solid' },
            { name: 'first-aid', class: 'fas fa-first-aid', category: 'solid' },
            { name: 'stethoscope', class: 'fas fa-stethoscope', category: 'solid' },
            { name: 'syringe', class: 'fas fa-syringe', category: 'solid' },
            { name: 'pills', class: 'fas fa-pills', category: 'solid' },
            { name: 'capsules', class: 'fas fa-capsules', category: 'solid' },
            { name: 'tablets', class: 'fas fa-tablets', category: 'solid' },
            { name: 'prescription', class: 'fas fa-prescription', category: 'solid' },
            { name: 'prescription-bottle', class: 'fas fa-prescription-bottle', category: 'solid' },
            { name: 'prescription-bottle-alt', class: 'fas fa-prescription-bottle-alt', category: 'solid' },
            { name: 'thermometer', class: 'fas fa-thermometer', category: 'solid' },
            { name: 'thermometer-half', class: 'fas fa-thermometer-half', category: 'solid' },
            { name: 'user-md', class: 'fas fa-user-md', category: 'solid' },
            { name: 'user-nurse', class: 'fas fa-user-nurse', category: 'solid' },
            { name: 'procedures', class: 'fas fa-procedures', category: 'solid' },
            { name: 'band-aid', class: 'fas fa-band-aid', category: 'solid' },
            { name: 'x-ray', class: 'fas fa-x-ray', category: 'solid' },
            { name: 'teeth', class: 'fas fa-teeth', category: 'solid' },
            { name: 'tooth', class: 'fas fa-tooth', category: 'solid' },
            // Shopping & E-commerce
            { name: 'shopping-cart', class: 'fas fa-shopping-cart', category: 'solid' },
            { name: 'shopping-basket', class: 'fas fa-shopping-basket', category: 'solid' },
            { name: 'shopping-bag', class: 'fas fa-shopping-bag', category: 'solid' },
            { name: 'cart-plus', class: 'fas fa-cart-plus', category: 'solid' },
            { name: 'cart-arrow-down', class: 'fas fa-cart-arrow-down', category: 'solid' },
            { name: 'cash-register', class: 'fas fa-cash-register', category: 'solid' },
            { name: 'tag', class: 'fas fa-tag', category: 'solid' },
            { name: 'tags', class: 'fas fa-tags', category: 'solid' },
            { name: 'box', class: 'fas fa-box', category: 'solid' },
            { name: 'box-open', class: 'fas fa-box-open', category: 'solid' },
            { name: 'boxes', class: 'fas fa-boxes', category: 'solid' },
            { name: 'cube', class: 'fas fa-cube', category: 'solid' },
            { name: 'cubes', class: 'fas fa-cubes', category: 'solid' },
            { name: 'gift', class: 'fas fa-gift', category: 'solid' },
            { name: 'gifts', class: 'fas fa-gifts', category: 'solid' },
            { name: 'percent', class: 'fas fa-percent', category: 'solid' },
            { name: 'percentage', class: 'fas fa-percentage', category: 'solid' },
            // Regular (outline) icons
            { name: 'file (regular)', class: 'far fa-file', category: 'regular' },
            { name: 'file-alt (regular)', class: 'far fa-file-alt', category: 'regular' },
            { name: 'folder (regular)', class: 'far fa-folder', category: 'regular' },
            { name: 'folder-open (regular)', class: 'far fa-folder-open', category: 'regular' },
            { name: 'envelope (regular)', class: 'far fa-envelope', category: 'regular' },
            { name: 'envelope-open (regular)', class: 'far fa-envelope-open', category: 'regular' },
            { name: 'bell (regular)', class: 'far fa-bell', category: 'regular' },
            { name: 'bookmark (regular)', class: 'far fa-bookmark', category: 'regular' },
            { name: 'calendar (regular)', class: 'far fa-calendar', category: 'regular' },
            { name: 'calendar-alt (regular)', class: 'far fa-calendar-alt', category: 'regular' },
            { name: 'clock (regular)', class: 'far fa-clock', category: 'regular' },
            { name: 'comment (regular)', class: 'far fa-comment', category: 'regular' },
            { name: 'comments (regular)', class: 'far fa-comments', category: 'regular' },
            { name: 'heart (regular)', class: 'far fa-heart', category: 'regular' },
            { name: 'star (regular)', class: 'far fa-star', category: 'regular' },
            { name: 'user (regular)', class: 'far fa-user', category: 'regular' },
            { name: 'user-circle (regular)', class: 'far fa-user-circle', category: 'regular' },
            { name: 'check-circle (regular)', class: 'far fa-check-circle', category: 'regular' },
            { name: 'times-circle (regular)', class: 'far fa-times-circle', category: 'regular' },
            { name: 'edit (regular)', class: 'far fa-edit', category: 'regular' },
            { name: 'trash-alt (regular)', class: 'far fa-trash-alt', category: 'regular' },
            { name: 'copy (regular)', class: 'far fa-copy', category: 'regular' },
            { name: 'clipboard (regular)', class: 'far fa-clipboard', category: 'regular' },
            { name: 'save (regular)', class: 'far fa-save', category: 'regular' },
            { name: 'image (regular)', class: 'far fa-image', category: 'regular' },
            { name: 'images (regular)', class: 'far fa-images', category: 'regular' },
            { name: 'eye (regular)', class: 'far fa-eye', category: 'regular' },
            { name: 'eye-slash (regular)', class: 'far fa-eye-slash', category: 'regular' },
            { name: 'smile (regular)', class: 'far fa-smile', category: 'regular' },
            { name: 'frown (regular)', class: 'far fa-frown', category: 'regular' },
            { name: 'meh (regular)', class: 'far fa-meh', category: 'regular' },
            { name: 'address-book (regular)', class: 'far fa-address-book', category: 'regular' },
            { name: 'address-card (regular)', class: 'far fa-address-card', category: 'regular' },
            { name: 'id-badge (regular)', class: 'far fa-id-badge', category: 'regular' },
            { name: 'id-card (regular)', class: 'far fa-id-card', category: 'regular' },
            { name: 'building (regular)', class: 'far fa-building', category: 'regular' },
            { name: 'hospital (regular)', class: 'far fa-hospital', category: 'regular' },
            { name: 'credit-card (regular)', class: 'far fa-credit-card', category: 'regular' },
            { name: 'money-bill-alt (regular)', class: 'far fa-money-bill-alt', category: 'regular' },
            { name: 'chart-bar (regular)', class: 'far fa-chart-bar', category: 'regular' },
            { name: 'lightbulb (regular)', class: 'far fa-lightbulb', category: 'regular' },
            { name: 'keyboard (regular)', class: 'far fa-keyboard', category: 'regular' },
            { name: 'compass (regular)', class: 'far fa-compass', category: 'regular' },
            { name: 'map (regular)', class: 'far fa-map', category: 'regular' },
            { name: 'paper-plane (regular)', class: 'far fa-paper-plane', category: 'regular' },
            { name: 'question-circle (regular)', class: 'far fa-question-circle', category: 'regular' },
            { name: 'life-ring (regular)', class: 'far fa-life-ring', category: 'regular' },
            { name: 'sun (regular)', class: 'far fa-sun', category: 'regular' },
            { name: 'moon (regular)', class: 'far fa-moon', category: 'regular' },
            { name: 'gem (regular)', class: 'far fa-gem', category: 'regular' },
            { name: 'handshake (regular)', class: 'far fa-handshake', category: 'regular' },
            { name: 'thumbs-up (regular)', class: 'far fa-thumbs-up', category: 'regular' },
            { name: 'thumbs-down (regular)', class: 'far fa-thumbs-down', category: 'regular' },
            // Brands
            { name: 'google', class: 'fab fa-google', category: 'brands' },
            { name: 'google-drive', class: 'fab fa-google-drive', category: 'brands' },
            { name: 'microsoft', class: 'fab fa-microsoft', category: 'brands' },
            { name: 'windows', class: 'fab fa-windows', category: 'brands' },
            { name: 'apple', class: 'fab fa-apple', category: 'brands' },
            { name: 'android', class: 'fab fa-android', category: 'brands' },
            { name: 'linux', class: 'fab fa-linux', category: 'brands' },
            { name: 'ubuntu', class: 'fab fa-ubuntu', category: 'brands' },
            { name: 'aws', class: 'fab fa-aws', category: 'brands' },
            { name: 'docker', class: 'fab fa-docker', category: 'brands' },
            { name: 'github', class: 'fab fa-github', category: 'brands' },
            { name: 'gitlab', class: 'fab fa-gitlab', category: 'brands' },
            { name: 'bitbucket', class: 'fab fa-bitbucket', category: 'brands' },
            { name: 'jira', class: 'fab fa-jira', category: 'brands' },
            { name: 'confluence', class: 'fab fa-confluence', category: 'brands' },
            { name: 'trello', class: 'fab fa-trello', category: 'brands' },
            { name: 'slack', class: 'fab fa-slack', category: 'brands' },
            { name: 'discord', class: 'fab fa-discord', category: 'brands' },
            { name: 'teams', class: 'fab fa-microsoft', category: 'brands' },
            { name: 'zoom', class: 'fas fa-video', category: 'brands' },
            { name: 'dropbox', class: 'fab fa-dropbox', category: 'brands' },
            { name: 'wordpress', class: 'fab fa-wordpress', category: 'brands' },
            { name: 'shopify', class: 'fab fa-shopify', category: 'brands' },
            { name: 'magento', class: 'fab fa-magento', category: 'brands' },
            { name: 'woocommerce', class: 'fab fa-woocommerce', category: 'brands' },
            { name: 'stripe', class: 'fab fa-stripe', category: 'brands' },
            { name: 'paypal', class: 'fab fa-paypal', category: 'brands' },
            { name: 'cc-visa', class: 'fab fa-cc-visa', category: 'brands' },
            { name: 'cc-mastercard', class: 'fab fa-cc-mastercard', category: 'brands' },
            { name: 'cc-amex', class: 'fab fa-cc-amex', category: 'brands' },
            { name: 'facebook', class: 'fab fa-facebook', category: 'brands' },
            { name: 'facebook-f', class: 'fab fa-facebook-f', category: 'brands' },
            { name: 'instagram', class: 'fab fa-instagram', category: 'brands' },
            { name: 'twitter', class: 'fab fa-twitter', category: 'brands' },
            { name: 'linkedin', class: 'fab fa-linkedin', category: 'brands' },
            { name: 'linkedin-in', class: 'fab fa-linkedin-in', category: 'brands' },
            { name: 'youtube', class: 'fab fa-youtube', category: 'brands' },
            { name: 'tiktok', class: 'fab fa-tiktok', category: 'brands' },
            { name: 'whatsapp', class: 'fab fa-whatsapp', category: 'brands' },
            { name: 'telegram', class: 'fab fa-telegram', category: 'brands' },
            { name: 'skype', class: 'fab fa-skype', category: 'brands' },
            { name: 'pinterest', class: 'fab fa-pinterest', category: 'brands' },
            { name: 'snapchat', class: 'fab fa-snapchat', category: 'brands' },
            { name: 'reddit', class: 'fab fa-reddit', category: 'brands' },
            { name: 'twitch', class: 'fab fa-twitch', category: 'brands' },
            { name: 'spotify', class: 'fab fa-spotify', category: 'brands' },
            { name: 'soundcloud', class: 'fab fa-soundcloud', category: 'brands' },
            { name: 'airbnb', class: 'fab fa-airbnb', category: 'brands' },
            { name: 'uber', class: 'fab fa-uber', category: 'brands' },
            { name: 'html5', class: 'fab fa-html5', category: 'brands' },
            { name: 'css3-alt', class: 'fab fa-css3-alt', category: 'brands' },
            { name: 'js', class: 'fab fa-js', category: 'brands' },
            { name: 'node', class: 'fab fa-node', category: 'brands' },
            { name: 'node-js', class: 'fab fa-node-js', category: 'brands' },
            { name: 'react', class: 'fab fa-react', category: 'brands' },
            { name: 'angular', class: 'fab fa-angular', category: 'brands' },
            { name: 'vuejs', class: 'fab fa-vuejs', category: 'brands' },
            { name: 'php', class: 'fab fa-php', category: 'brands' },
            { name: 'laravel', class: 'fab fa-laravel', category: 'brands' },
            { name: 'python', class: 'fab fa-python', category: 'brands' },
            { name: 'java', class: 'fab fa-java', category: 'brands' },
            { name: 'sass', class: 'fab fa-sass', category: 'brands' },
            { name: 'bootstrap', class: 'fab fa-bootstrap', category: 'brands' },
            { name: 'npm', class: 'fab fa-npm', category: 'brands' },
            { name: 'yarn', class: 'fab fa-yarn', category: 'brands' },
            { name: 'git-alt', class: 'fab fa-git-alt', category: 'brands' },
            { name: 'salesforce', class: 'fab fa-salesforce', category: 'brands' },
            { name: 'hubspot', class: 'fab fa-hubspot', category: 'brands' },
            { name: 'mailchimp', class: 'fab fa-mailchimp', category: 'brands' },
            { name: 'figma', class: 'fab fa-figma', category: 'brands' },
            { name: 'sketch', class: 'fab fa-sketch', category: 'brands' },
            { name: 'adobe', class: 'fab fa-adobe', category: 'brands' },
            { name: 'invision', class: 'fab fa-invision', category: 'brands' },
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

            // Add click handlers
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

        // Initialize icons when modal opens
        document.getElementById('iconPickerModal').addEventListener('shown.bs.modal', function() {
            renderIcons();
            iconSearchInput.focus();
        });

        // Search functionality
        iconSearchInput.addEventListener('input', function(e) {
            e.stopPropagation();
            const category = document.querySelector('input[name="iconCategory"]:checked').value;
            renderIcons(this.value, category);
        });

        // Prevent form submission on enter in search
        iconSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // Clear search
        clearIconSearch.addEventListener('click', function() {
            iconSearchInput.value = '';
            const category = document.querySelector('input[name="iconCategory"]:checked').value;
            renderIcons('', category);
            iconSearchInput.focus();
        });

        // Category filter
        document.querySelectorAll('input[name="iconCategory"]').forEach(radio => {
            radio.addEventListener('change', function() {
                renderIcons(iconSearchInput.value, this.value);
            });
        });

        // Confirm icon selection
        confirmIconBtn.addEventListener('click', function() {
            if (selectedIcon) {
                iconInput.value = selectedIcon;
                selectedIconPreview.className = selectedIcon;
                bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
                // Reset for next time
                selectedIcon = null;
                selectedIconName.textContent = 'Ninguno';
                confirmIconBtn.disabled = true;
                iconSearchInput.value = '';
            }
        });

        // Update icon preview when template is applied
        const originalIconInput = iconInput.value;
        if (originalIconInput) {
            selectedIconPreview.className = originalIconInput;
        }

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
