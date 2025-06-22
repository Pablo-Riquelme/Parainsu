<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">

    <!-- Bootstrap CSS (este sí debe estar) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom Project CSS Files -->
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <!-- Vite Assets (Esto ya maneja resources/sass/app.scss y resources/js/app.js, que deberían importar Bootstrap JS UNA VEZ) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles') {{-- Para CSS adicional de vistas específicas --}}
</head>
<body>
    <div id="app">
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
                    {{-- Volver al Menú Principal (Opciones) - Visible para todos los roles autenticados --}}
                    {{-- Esta es la ruta al dashboard o home principal --}}
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

                         {{-- NEW: Botón "Mensajes" - Visible para todos los roles autenticados --}}
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

        <main class="main-content"> {{-- Agregada la clase 'main-content' --}}
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>> --}}

    <script>
        
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
                        timer: 3000, // Auto-close after 3 seconds
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
                    e.preventDefault(); // Prevents immediate form submission
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
                            this.closest('form').submit(); // Submits the form if confirmed
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts') {{-- Aquí se insertarán scripts específicos de las vistas, como Select2 --}}
</body>
</html>
