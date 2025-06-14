<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Parainsu - Clínica Puerto Montt</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="full-screen-bg"
         style="background-image: url('{{ asset('images/Fondo1.jpg') }}');
                background-color: rgba(74, 198, 247, 0.49);
                background-blend-mode: overlay;">
        <header class="header">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Clínica Puerto Montt">
            </div>
            <div class="header-logo2">
                <img src="{{ asset('images/Parainsu.png') }}" alt="Parainsu">
            </div>
        </header>

        <main class="main-content">
            <div class="main-content">
                <div class="login-form-container">
                    <h2 class="login-form-title">
                        Bienvenido
                    </h2>
                    <form class="space-y-4" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Usuario
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                                placeholder="Ingrese Usuario"
                                class="form-input"
                            >
                            @error('email')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">
                                Contraseña
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Ingrese Contraseña"
                                class="form-input"
                            >
                            @error('password')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="login-button">
                            Ingresar
                        </button>
                    </form>
                </div>

                <div class="info-box">
                    <h2 class="info-box-title">¿Qué es Parainsu?</h2>
                    <p class="info-box-text">
                        Parainsu es un sistema de gestión de inventario creado para optimizar el control
                        de recursos tecnológicos y médicos en la Clínica Puerto Montt. Surge como una solución a la
                        desorganización y pérdida de equipos, facilitando una administración más eficiente, segura
                        y centralizada dentro del entorno clínico.
                    </p>
                </div>
            </div>
        </main>
        <footer class="footer">
            © {{ date('Y') }} Sistema de Gestión Parainsu. Todos los derechos reservados.
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
