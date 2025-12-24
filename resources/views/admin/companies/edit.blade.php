@extends('admin.layouts.app')

@section('title', 'Editar Empresa')

@section('page-header')
    <h1>Editar Empresa</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Empresas</a></li>
            <li class="breadcrumb-item active">{{ $company->name }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<form action="{{ route('admin.companies.update', $company) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-building me-2"></i>Información de la Empresa
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nombre de la Empresa *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $company->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email de Contacto *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tax_id" class="form-label">RFC / Tax ID</label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}">
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_admins" class="form-label">Máximo de Administradores</label>
                            <input type="number" class="form-control @error('max_admins') is-invalid @enderror" id="max_admins" name="max_admins" value="{{ old('max_admins', $company->max_admins) }}" min="1" max="10">
                            @error('max_admins')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                <option value="pending" {{ old('status', $company->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="suspended" {{ old('status', $company->status) == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Info Card --}}
            <div class="card mb-3">
                <div class="card-header">Información</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Creada:</small>
                        <div>{{ $company->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Actualizada:</small>
                        <div>{{ $company->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-outline-secondary">
                    Ver Detalles
                </a>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                    Volver al Listado
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
