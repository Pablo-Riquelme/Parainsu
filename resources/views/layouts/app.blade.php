<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Considera si necesitas todos estos. custom.css debería ser el principal para overrides. --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> {{-- Si es de Vite, @vite ya lo maneja --}}
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> {{-- Este debería tener tus overrides específicos --}}

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
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
                    <li class="nav-item">
                        {{-- El enlace "Opciones" suele ser el Home --}}
                        <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> Opciones
                        </a>
                    </li>

                    {{-- === Bloque para Administrador TI === --}}
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-chart-line"></i> Dashboard Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Gestionar Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-tag"></i> Gestionar Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DE PERMISOS --}}
                                <i class="fas fa-key"></i> Gestionar Permisos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('equipos-ti.index') ? 'active' : '' }}" href="{{ route('equipos-ti.index') }}">
                                <i class="fas fa-desktop"></i> Gestionar Equipos TI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('insumos-medicos.index') ? 'active' : '' }}" href="{{ route('insumos-medicos.index') }}">
                                <i class="fas fa-boxes"></i> Gestionar Insumos Médicos
                            </a>
                        </li>
                    @endif

                    {{-- === Bloque para Bodega (visible solo si NO es Admin TI) === --}}
                    {{-- Si un Admin TI también es Bodega, no necesitamos duplicar los enlaces para ellos. --}}
                    @if(auth()->user()->isBodega() && !auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('insumos-medicos.index') }}"> {{-- Para Bodega --}}
                                <i class="fas fa-boxes"></i> Gestionar Insumos Médicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DE VER INVENTARIO --}}
                                <i class="fas fa-boxes"></i> Ver Inventario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DE GESTIONAR ENTRADAS --}}
                                <i class="fas fa-sign-in-alt"></i> Gestionar Entradas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DE GESTIONAR SALIDAS --}}
                                <i class="fas fa-sign-out-alt"></i> Gestionar Salidas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DE GESTIONAR PROVEEDORES --}}
                                <i class="fas fa-truck"></i> Gestionar Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> {{-- CAMBIAR A LA RUTA REAL DEL DASHBOARD BODEGA --}}
                                <i class="fas fa-warehouse"></i> Dashboard Bodega
                            </a>
                        </li>
                    @endif

                    {{-- === Bloque para Usuario Normal (visible si no es Admin TI ni Bodega) === --}}
                    @if(!auth()->user()->isAdmin() && !auth()->user()->isBodega())
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                <i class="fas fa-user"></i> Ver Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                                <i class="fas fa-user-circle"></i> Dashboard Usuario
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

        <main class="">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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

        // Script para el SweetAlert de confirmación de eliminación (opcional, si lo usas en las vistas)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-alert').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault(); // Previene el envío inmediato del formulario
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
                            this.closest('form').submit(); // Envía el formulario si se confirma
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts') {{-- Aquí se insertarán scripts específicos de las vistas, como Select2 --}}
</body>
</html>