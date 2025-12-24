@extends('admin.layouts.app')

@section('title', 'Configuración')

@section('page-header')
    <h1>Configuración</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Configuración</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3">
        {{-- Settings Nav --}}
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-building me-2"></i>General
                    </a>
                    <a href="#branding" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-palette me-2"></i>Branding
                    </a>
                    <a href="#theme" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-paint-brush me-2"></i>Tema
                    </a>
                    <a href="#cache" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-database me-2"></i>Cache
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="tab-content">
            {{-- General --}}
            <div class="tab-pane fade show active" id="general">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-building me-2"></i>Información General
                    </div>
                    <form action="{{ route('admin.settings.general') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre de la Empresa</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $company->name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email de Contacto</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $company->email }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $company->phone }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label">Sitio Web</label>
                                    <input type="url" class="form-control" id="website" name="website" value="{{ $company->website }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección</label>
                                <textarea class="form-control" id="address" name="address" rows="2">{{ $company->address }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Branding --}}
            <div class="tab-pane fade" id="branding">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-image me-2"></i>Logo
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-3">
                                @if($config->logo_url)
                                    <img src="{{ Storage::url($config->logo_url) }}" alt="Logo" class="img-fluid" style="max-height: 100px;">
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>Sin logo</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" class="form-control" name="logo" accept="image/*">
                                        <small class="text-muted">Formatos: PNG, JPG, SVG. Máx: 2MB</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i>Subir Logo
                                    </button>
                                    @if($config->logo_url)
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('delete-logo-form').submit();">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                    @endif
                                </form>
                                <form id="delete-logo-form" action="{{ route('admin.settings.logo.delete') }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-palette me-2"></i>Contenido
                    </div>
                    <form action="{{ route('admin.settings.branding') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="meta_title" class="form-label">Título del Sitio</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $config->meta_title }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="header_text" class="form-label">Texto del Encabezado</label>
                                    <input type="text" class="form-control" id="header_text" name="header_text" value="{{ $config->header_text }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Descripción (SEO)</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2">{{ $config->meta_description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="footer_text" class="form-label">Texto del Pie de Página</label>
                                <input type="text" class="form-control" id="footer_text" name="footer_text" value="{{ $config->footer_text }}">
                            </div>
                            <hr>
                            <h6 class="mb-3">Visibilidad de Secciones</h6>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="show_calendar" name="show_calendar" value="1" {{ $config->show_calendar ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_calendar">Mostrar Calendario</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="show_news_ticker" name="show_news_ticker" value="1" {{ $config->show_news_ticker ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_news_ticker">Mostrar Noticias</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="show_contacts" name="show_contacts" value="1" {{ $config->show_contacts ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_contacts">Mostrar Directorio</label>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Theme --}}
            <div class="tab-pane fade" id="theme">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-paint-brush me-2"></i>Colores del Tema
                    </div>
                    <form action="{{ route('admin.settings.theme') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="primary_color" class="form-label">Color Primario</label>
                                    <input type="color" class="form-control form-control-color w-100" id="primary_color" name="primary_color" value="{{ $config->primary_color ?? '#4c78dd' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="secondary_color" class="form-label">Color Secundario</label>
                                    <input type="color" class="form-control form-control-color w-100" id="secondary_color" name="secondary_color" value="{{ $config->secondary_color ?? '#6c757d' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="accent_color" class="form-label">Color de Acento</label>
                                    <input type="color" class="form-control form-control-color w-100" id="accent_color" name="accent_color" value="{{ $config->accent_color ?? '#f3b760' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="background_color" class="form-label">Fondo</label>
                                    <input type="color" class="form-control form-control-color w-100" id="background_color" name="background_color" value="{{ $config->background_color ?? '#f0f2f5' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="text_color" class="form-label">Texto</label>
                                    <input type="color" class="form-control form-control-color w-100" id="text_color" name="text_color" value="{{ $config->text_color ?? '#1a1f2c' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="module_bg_color" class="form-label">Fondo Módulos</label>
                                    <input type="color" class="form-control form-control-color w-100" id="module_bg_color" name="module_bg_color" value="{{ $config->module_bg_color ?? '#ffffff' }}">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Tema
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Cache --}}
            <div class="tab-pane fade" id="cache">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-database me-2"></i>Configuración de Cache</span>
                        <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Limpiar Cache
                            </button>
                        </form>
                    </div>
                    <form action="{{ route('admin.settings.cache') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <p class="text-muted mb-4">Configura el tiempo de vida (TTL) del cache para cada sección en segundos.</p>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="modules_ttl" class="form-label">Módulos</label>
                                    <input type="number" class="form-control" id="modules_ttl" name="modules_ttl" value="{{ $cacheSettings->modules_ttl ?? 600 }}" min="60" max="86400">
                                    <small class="text-muted">60 - 86400 seg</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contacts_ttl" class="form-label">Contactos</label>
                                    <input type="number" class="form-control" id="contacts_ttl" name="contacts_ttl" value="{{ $cacheSettings->contacts_ttl ?? 600 }}" min="60" max="86400">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="events_ttl" class="form-label">Eventos</label>
                                    <input type="number" class="form-control" id="events_ttl" name="events_ttl" value="{{ $cacheSettings->events_ttl ?? 600 }}" min="60" max="86400">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="news_ttl" class="form-label">Noticias</label>
                                    <input type="number" class="form-control" id="news_ttl" name="news_ttl" value="{{ $cacheSettings->news_ttl ?? 60 }}" min="10" max="3600">
                                    <small class="text-muted">10 - 3600 seg</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="banner_ttl" class="form-label">Banners</label>
                                    <input type="number" class="form-control" id="banner_ttl" name="banner_ttl" value="{{ $cacheSettings->banner_ttl ?? 600 }}" min="60" max="86400">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="config_ttl" class="form-label">Configuración</label>
                                    <input type="number" class="form-control" id="config_ttl" name="config_ttl" value="{{ $cacheSettings->config_ttl ?? 3600 }}" min="60" max="86400">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
