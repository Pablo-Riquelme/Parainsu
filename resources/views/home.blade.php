@extends('layouts.app')

@section('content')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
<div class="container-fluid">
    <div class="row">
        <nav class="top-navbar">
            <div class="navbar-icon-left-container">
                <img src="{{ asset('images/logo.png') }}" alt="Icono Izquierdo" class="navbar-icon-left">
            </div>
            <div class="user-info">
                Bienvenido, {{ auth()->user()->name }}
            </div>
            <div class="navbar-icon-right-container">
                <img src="{{ asset('images/parainsu.png') }}" alt="Icono Derecho" class="navbar-icon-right">
            </div>
</nav>

        <nav id="sidebar" class="sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home"></i> Opciones
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Gestionar Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('equipos-ti.index') }}">
                                <i class="fas fa-desktop"></i> Gestionar Equipos TI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-key"></i> Gestionar Permisos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-chart-line"></i> Dashboard Admin
                            </a>
                        </li>
                    @elseif(auth()->user()->isBodega())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-boxes"></i> Ver Inventario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-sign-in-alt"></i> Gestionar Entradas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-sign-out-alt"></i> Gestionar Salidas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-truck"></i> Gestionar Proveedores
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-warehouse"></i> Dashboard Bodega
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.show') }}">
                                <i class="fas fa-user"></i> Ver Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.dashboard') }}">
                                <i class="fas fa-user-circle"></i>  Dashboard Usuario
                            </a>
                        </li>
                    @endif
                </ul>
                 <div class="logout-container">
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </button>
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </nav>

        <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @yield('dashboard_content')
             <div class="welcome-message">
                <img src="{{ asset('images/fondo1.jpg') }}" alt="Imagen de Bienvenida" class="welcome-img">
            </div>
        </main>
    </div>
</div>
@endsection
