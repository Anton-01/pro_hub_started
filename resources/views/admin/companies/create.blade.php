@extends('admin.layouts.app')

@section('title', 'Nueva Empresa')

@section('page-header')
    <h1>Nueva Empresa</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Empresas</a></li>
            <li class="breadcrumb-item active">Nueva</li>
        </ol>
    </nav>
@endsection

@section('content')
<form action="{{ route('admin.companies.store') }}" method="POST">
    @csrf

    <div class="row">
        {{-- Company Info --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-building me-2"></i>Información de la Empresa
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nombre de la Empresa *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="auto-generado">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email de Contacto *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tax_id" class="form-label">RFC / Tax ID</label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_admins" class="form-label">Máximo de Administradores</label>
                            <input type="number" class="form-control @error('max_admins') is-invalid @enderror" id="max_admins" name="max_admins" value="{{ old('max_admins', 3) }}" min="1" max="10">
                            @error('max_admins')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Primary Admin --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-shield me-2"></i>Administrador Principal
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Este usuario será el administrador principal de la empresa y podrá crear otros administradores.</p>

                    <div class="mb-3">
                        <label for="admin_name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control @error('admin_name') is-invalid @enderror" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                        @error('admin_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_last_name" class="form-label">Apellido</label>
                        <input type="text" class="form-control @error('admin_last_name') is-invalid @enderror" id="admin_last_name" name="admin_last_name" value="{{ old('admin_last_name') }}">
                        @error('admin_last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                        @error('admin_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" required>
                        @error('admin_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_password_confirmation" class="form-label">Confirmar Contraseña *</label>
                        <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Crear Empresa
                </button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
