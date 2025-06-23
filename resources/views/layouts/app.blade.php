<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Carga tus CSS globales aquí. El orden importa: custom.css primero, luego global-layout.css --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> {{-- Tus estilos para card, form, table, etc. --}}
    <link href="{{ asset('css/global-layout.css') }}" rel="stylesheet"> {{-- NUEVO: Estilos para top-navbar y sidebar --}}
    <link href="{{ asset('css/index.css') }}" rel="stylesheet"> {{-- Si index.css tiene estilos globales, mantenlo --}}

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles') {{-- Aquí se insertarán estilos específicos de cada vista (como los del home) --}}
</head>
<body @yield('body_attributes')>
    <div id="app">
        {{-- Barra Superior (HTML global) --}}
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

        <div class="d-flex" style="flex-grow: 1;"> {{-- Contenedor flex para sidebar y main-content-wrapper --}}
            {{-- Barra Lateral (HTML global) --}}
            <nav id="sidebar" class="sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    <i class="fas fa-home"></i> Opciones
                                </a>
                            </li>

                            {{-- Administrar Equipos TI - Rol: admin_ti --}}
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('equipos-ti.index') ? 'active' : '' }}" href="{{ route('equipos-ti.index') }}">
                                        <i class="fas fa-desktop"></i> Administrar Equipos TI
                                    </a>
                                </li>
                            @endif

                            {{-- Administrar Insumos Médicos - Rol: admin_ti y bodega --}}
                            @if(auth()->user()->isAdmin() || auth()->user()->isBodega())
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('insumos-medicos.index') ? 'active' : '' }}" href="{{ route('insumos-medicos.index') }}">
                                        <i class="fas fa-boxes"></i> Administrar Insumos Médicos
                                    </a>
                                </li>
                            @endif

                            {{-- Administrar Usuarios - Rol: admin_ti --}}
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <i class="fas fa-users"></i> Administrar Usuarios
                                    </a>
                                </li>
                            @endif

                            {{-- Ver Movimientos - Visible para todos los roles autenticados --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('movimientos.index') ? 'active' : '' }}" href="{{ route('movimientos.index') }}">
                                    <i class="fas fa-history"></i> Ver Movimientos
                                </a>
                            </li>

                            {{-- Botón "Mensajes" - Visible para todos los roles autenticados --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('chats.index') ? 'active' : '' }}" href="{{ route('chats.index') }}">
                                    <i class="fas fa-comments"></i> Mensajes
                                </a>
                            </li>
                        @endauth
                    </ul>

                    {{-- Contenedor de Cerrar Sesión --}}
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

            {{-- Área de Contenido Principal (donde se inyectan las vistas hijas) --}}
            {{-- Las clases `main-content-wrapper` se definen en global-layout.css --}}
            <main class="main-content-wrapper">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <script>
        // Welcome Message with SweetAlert2
        window.onload = function() {
            @if (session('show_welcome_message'))
                @if (auth()->check())
                    let welcomeText = '';
                    @if (auth()->user()->isAdmin())
                        welcomeText = '¡Bienvenido Administrador TI!';
                    @elseif (auth()->user()->isBodega())
                        welcomeText = '¡Bienvenido Encargado de Bodega!';
                    @else
                        welcomeText = '¡Bienvenido Usuario!';
                    @endif

                    Swal.fire({
                        title: '¡Hola!',
                        text: welcomeText,
                        icon: 'success',
                        confirmButtonText: '¡Entendido!',
                        timer: 3000,
                        showClass: {
                            popup: `
                                animate__animated
                                animate__fadeInDown
                                animate__faster
                            `
                        },
                        hideClass: {
                            popup: `
                                animate__animated
                                animate__fadeOutUp
                                animate__faster
                            `
                        }
                    });
                @endif
                @php session()->forget('show_welcome_message'); @endphp
            @endif
        };

        // Script for SweetAlert2 delete confirmation (optional, if you use it in your views)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-alert').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.closest('form').submit();
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts') {{-- Aquí se insertarán scripts específicos de las vistas --}}
</body>
</html>
