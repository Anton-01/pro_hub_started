<header class="main-header">
    {{-- Left Side --}}
    <div class="header-nav">
        <button type="button" class="btn-toggle-sidebar" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    {{-- Right Side --}}
    <div class="header-actions">
        {{-- Company Indicator (for admins) --}}
        @if(!auth()->user()->isSuperAdmin() && auth()->user()->company)
            <div class="d-none d-md-flex align-items-center text-muted small">
                <i class="fas fa-building me-2"></i>
                {{ auth()->user()->company->name }}
            </div>
        @endif

        {{-- User Dropdown --}}
        <div class="dropdown user-dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                @if(auth()->user()->avatar_url)
                    <img src="{{ Storage::url(auth()->user()->avatar_url) }}" alt="Avatar" class="avatar">
                @else
                    <div class="avatar-placeholder">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                <i class="fas fa-chevron-down ms-1 small"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text">
                        <strong>{{ auth()->user()->full_name }}</strong><br>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                        <i class="fas fa-user me-2"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog me-2"></i> Configuración
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
