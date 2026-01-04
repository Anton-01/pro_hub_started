@extends('admin.layouts.app')

@section('title', 'Importar Contactos')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Importación Masiva de Contactos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Contactos</a></li>
                    <li class="breadcrumb-item active">Importar</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- Instrucciones --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Instrucciones
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">
                            <strong>Descarga la plantilla Excel</strong> haciendo clic en el botón de abajo.
                        </li>
                        <li class="mb-2">
                            <strong>Completa la información</strong> de los contactos en el archivo Excel.
                            <ul class="mt-1">
                                <li>El campo <strong>"Nombre"</strong> es obligatorio.</li>
                                <li>Si proporcionas un <strong>email existente</strong>, el contacto será actualizado.</li>
                                <li>El campo <strong>"Estado"</strong> puede ser: <code>activo</code> o <code>inactivo</code>.</li>
                                <li>Los demás campos son opcionales.</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Guarda el archivo</strong> y súbelo usando el formulario de abajo.
                        </li>
                        <li>
                            El sistema procesará el archivo y te mostrará un <strong>resumen</strong> de la importación.
                        </li>
                    </ol>
                </div>
            </div>

            {{-- Descargar Plantilla --}}
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-file-excel text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Descargar Plantilla de Excel</h5>
                    <p class="text-muted mb-4">
                        Descarga la plantilla con el formato correcto y ejemplos de datos.
                    </p>
                    <a href="{{ route('admin.contacts.template') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-download me-2"></i>Descargar Plantilla (.xlsx)
                    </a>
                </div>
            </div>

            {{-- Resumen de Importación --}}
            @if(session('import_summary'))
                <div class="card mb-4 border-{{ session('import_errors') ? 'warning' : 'success' }}">
                    <div class="card-header bg-{{ session('import_errors') ? 'warning' : 'success' }} text-white">
                        <i class="fas fa-chart-bar me-2"></i>Resumen de Importación
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ session('import_summary')['success'] }}</h3>
                                    <p class="text-muted mb-0">Creados</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-sync-alt text-info" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ session('import_summary')['updated'] }}</h3>
                                    <p class="text-muted mb-0">Actualizados</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ session('import_summary')['errors'] }}</h3>
                                    <p class="text-muted mb-0">Errores</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <strong>Total procesado:</strong> {{ session('import_summary')['total'] }} registros
                        </div>
                    </div>
                </div>
            @endif

            {{-- Errores de Importación --}}
            @if(session('import_errors'))
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-exclamation-circle me-2"></i>Errores Encontrados
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Se encontraron {{ count(session('import_errors')) }} filas con errores. Por favor, corrige los errores y vuelve a importar.
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead class="table-light sticky-top">
                                <tr>
                                    <th>Fila</th>
                                    <th>Errores</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(session('import_errors') as $error)
                                    <tr>
                                        <td class="align-middle">
                                            <strong>Fila {{ $error['row'] }}</strong>
                                        </td>
                                        <td>
                                            <ul class="mb-0">
                                                @foreach($error['errors'] as $message)
                                                    <li class="text-danger">{{ $message }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Formulario de Importación --}}
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-upload me-2"></i>Subir Archivo
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contacts.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label">Archivo Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tamaño máximo: 10MB. Formatos permitidos: .xlsx, .xls, .csv
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div id="fileInfo" class="text-muted" style="display: none;">
                                <i class="fas fa-file-alt me-2"></i>
                                <span id="fileName"></span>
                                <span class="badge bg-secondary ms-2" id="fileSize"></span>
                            </div>
                            <div class="ms-auto">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-upload me-2"></i>Importar Contactos
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileInput = e.target;
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                fileName.textContent = file.name;
                // Formatear tamaño del archivo
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                fileSize.textContent = sizeInMB + ' MB';
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        });
        // Mostrar spinner al enviar el formulario
        document.getElementById('importForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
        });
    </script>
@endpush
