@extends('admin.layouts.app')

@section('title', 'Mi Perfil')

@section('page-header')
    <h1>Mi Perfil</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Mi Perfil</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row">
    {{-- Profile Info --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($user->avatar_url)
                        <img src="{{ Storage::url($user->avatar_url) }}" alt="Avatar" class="avatar-lg rounded-circle">
                    @else
                        <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h4 class="mb-1">{{ $user->full_name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->role == 'super_admin' ? 'danger' : 'primary' }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </div>
        </div>

        {{-- Avatar Upload --}}
        <div class="card mb-4">
            <div class="card-header">Cambiar Avatar</div>
            <div class="card-body">
                <form action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Formatos: JPG, PNG. Máx: 2MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload me-2"></i>Subir Avatar
                    </button>
                </form>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="card">
            <div class="card-header">Información de la Cuenta</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Empresa</small>
                    <div>{{ $user->company->name ?? '-' }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Registrado</small>
                    <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <small class="text-muted">Último acceso</small>
                    <div>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Forms --}}
    <div class="col-lg-8">
        {{-- Update Profile --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>Información Personal
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        <small class="text-muted">El email no se puede cambiar</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-lock me-2"></i>Cambiar Contraseña
            </div>
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Contraseña Actual *</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nueva Contraseña *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
