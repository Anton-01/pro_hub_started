@extends('admin.layouts.guest')

@section('title', 'Recuperar Contraseña')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <div class="logo-icon">
            <i class="fas fa-key"></i>
        </div>
        <h1>Recuperar Contraseña</h1>
        <p>Te enviaremos un enlace para restablecer tu contraseña</p>
    </div>

    @include('admin.partials.alerts')

    <form class="auth-form" method="POST" action="{{ route('admin.password.email') }}">
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

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i>
            Enviar Enlace
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
