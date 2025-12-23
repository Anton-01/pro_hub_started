<aside class="sidebar">
    {{-- Logo --}}
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <span>{{ config('app.name') }}</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        {{-- Dashboard --}}
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>

        @if(auth()->user()->isSuperAdmin())
            {{-- Super Admin Section --}}
            <div class="nav-header">Super Admin</div>

            <div class="nav-item">
                <a href="{{ route('admin.companies.index') }}" class="nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Empresas</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.statistics.index') }}" class="nav-link {{ request()->routeIs('admin.statistics*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Estadísticas</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Logs de Actividad</span>
                </a>
            </div>
        @endif

        {{-- Content Management --}}
        <div class="nav-header">Gestión de Contenido</div>

        <div class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') && !request()->routeIs('admin.users.profile*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.modules.index') }}" class="nav-link {{ request()->routeIs('admin.modules*') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Módulos</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                <i class="fas fa-address-book"></i>
                <span>Directorio</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Eventos</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.news.index') }}" class="nav-link {{ request()->routeIs('admin.news*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i>
                <span>Noticias</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
                <i class="fas fa-images"></i>
                <span>Banners</span>
            </a>
        </div>

        {{-- Settings --}}
        <div class="nav-header">Configuración</div>

        <div class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </div>
    </nav>
</aside>
