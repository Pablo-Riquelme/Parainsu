<nav class="navbar navbar-expand-lg navbar-light admin-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard Admin') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i> {{ __('Gestionar Usuarios') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('roles.index') }}">
                        <i class="fas fa-user-tag"></i> {{ __('Gestionar Roles') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('permissions.index') }}">
                        <i class="fas fa-key"></i> {{ __('Gestionar Permisos') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('settings.index') }}">
                        <i class="fas fa-cog"></i> {{ __('Configuración') }}
                    </a>
                </li>
                <li class="nav-item logout-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Cerrar Sesión') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
