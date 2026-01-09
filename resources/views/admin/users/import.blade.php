@extends('admin.layouts.app')

@section('title', 'Importar Usuarios')

@section('page-header')
    <h1>Importar Usuarios</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
            <li class="breadcrumb-item active">Importar</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Alerta de éxito/error --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Botón para descargar reporte si está disponible --}}
        @if(session('show_download'))
            <div class="alert alert-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-file-alt me-2"></i>
                        <strong>Reporte disponible:</strong> Descarga el archivo TXT con los resultados de la importación.
                    </div>
                    <a href="{{ route('admin.users.import.report') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Descargar Reporte TXT
                    </a>
                </div>
            </div>
        @endif

        {{-- Resumen de importación --}}
        @if(session('import_summary'))
            @php $summary = session('import_summary'); @endphp
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i>Resumen de Importación
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-success bg-opacity-10">
                                <h3 class="text-success mb-1">{{ $summary['success'] }}</h3>
                                <small class="text-muted">Usuarios Registrados</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-danger bg-opacity-10">
                                <h3 class="text-danger mb-1">{{ $summary['errors'] }}</h3>
                                <small class="text-muted">Con Errores</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-primary bg-opacity-10">
                                <h3 class="text-primary mb-1">{{ $summary['total'] }}</h3>
                                <small class="text-muted">Total Procesados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Errores de importación --}}
        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-circle me-2"></i>Registros con Errores
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Fila</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('import_errors') as $error)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $error['row'] }}</span></td>
                                        <td>{{ $error['name'] }}</td>
                                        <td>{{ $error['email'] }}</td>
                                        <td>
                                            @foreach($error['errors'] as $errMsg)
                                                <small class="text-danger d-block">{{ $errMsg }}</small>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card de información --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-info-circle me-2"></i>Información Importante
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Los usuarios se crearán con el rol <strong>"Usuario"</strong> y estado <strong>"Aprobado"</strong></li>
                    <li>Las contraseñas se generan automáticamente (12 caracteres con mayúsculas, minúsculas, números y símbolos)</li>
                    <li>El <strong>correo electrónico debe ser único</strong> - los duplicados serán rechazados</li>
                    <li>Al finalizar, podrás descargar un archivo <strong>TXT</strong> con los resultados de la importación</li>
                    <li>El archivo TXT incluirá las credenciales de los usuarios registrados exitosamente</li>
                </ul>
            </div>
        </div>

        {{-- Card principal de importación --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-import me-2"></i>Importar desde Excel
            </div>
            <div class="card-body">
                {{-- Descargar plantilla --}}
                <div class="mb-4 p-3 bg-light rounded">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-file-excel text-success me-2"></i>Plantilla de Excel
                            </h6>
                            <p class="text-muted mb-0 small">
                                Descarga la plantilla con el formato correcto para la importación.
                            </p>
                        </div>
                        <a href="{{ route('admin.users.template') }}" class="btn btn-success">
                            <i class="fas fa-download me-1"></i>Descargar Plantilla
                        </a>
                    </div>
                </div>

                {{-- Formulario de importación --}}
                <form action="{{ route('admin.users.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label">Archivo Excel</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Formatos aceptados: .xlsx, .xls, .csv (máximo 10MB)
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Importar Usuarios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Instrucciones --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-question-circle me-2"></i>Instrucciones
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Descarga la <strong>plantilla de Excel</strong> haciendo clic en el botón verde</li>
                    <li class="mb-2">Completa la plantilla con los datos de los usuarios:
                        <ul>
                            <li><strong>Nombre</strong> (obligatorio)</li>
                            <li><strong>Apellido</strong> (opcional)</li>
                            <li><strong>Correo</strong> (obligatorio - debe ser único)</li>
                        </ul>
                    </li>
                    <li class="mb-2">Guarda el archivo en formato <strong>.xlsx, .xls o .csv</strong></li>
                    <li class="mb-2">Selecciona el archivo y haz clic en <strong>"Importar Usuarios"</strong></li>
                    <li class="mb-2">Descarga el <strong>reporte TXT</strong> con las credenciales de acceso</li>
                    <li>Comparte las credenciales de forma segura con los usuarios</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
