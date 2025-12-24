@extends('admin.layouts.guest')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <div class="logo-icon">
            <i class="fas fa-lock"></i>
        </div>
        <h1>Nueva Contraseña</h1>
        <p>Ingresa tu nueva contraseña</p>
    </div>

    @include('admin.partials.alerts')

    <form class="auth-form" method="POST" action="{{ route('admin.password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Mínimo 8 caracteres"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input
                type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>
            Actualizar Contraseña
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('admin.login') }}">
            <i class="fas fa-arrow-left me-1"></i>
            Volver al login
        </a>
    </div>
</div>
@endsection
