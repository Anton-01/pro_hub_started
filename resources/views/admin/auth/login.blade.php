@extends('admin.layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <div class="logo-icon">
            <i class="fas fa-building"></i>
        </div>
        <h1>{{ config('app.name') }}</h1>
        <p>Panel de Administración</p>
    </div>

    @include('admin.partials.alerts')

    <form class="auth-form" method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@ejemplo.com"
                required
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="••••••••"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Recordar sesión</label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>
            Iniciar Sesión
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('admin.password.request') }}">¿Olvidaste tu contraseña?</a>
    </div>
</div>
@endsection
