{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

{{-- El section content en layouts/app.blade.php ahora maneja el padding global. --}}
{{-- Aquí, el contenido del home es el que se inyecta en el main-content-wrapper --}}

@section('content')
    {{-- Este div row ahora organiza el contenido del home. Se ejecutará DENTRO de main-content-wrapper --}}
    <div class="row h-100 gx-4"> {{-- Agregamos gx-4 para un gap horizontal entre columnas, si Bootstrap lo soporta. O usa mt-lg-0 y ms-lg-auto en las columnas --}}
        {{-- Columna principal del dashboard (sin background-image aquí) --}}
        <div class="col-lg-8 d-flex flex-column home-main-content-col"> {{-- CAMBIADO a col-lg-8 --}}
            @yield('dashboard_content') {{-- Si tienes contenido inyectado aquí desde otras vistas --}}

            {{-- TARJETA DE BIENVENIDA: Ahora será un rectángulo horizontal --}}
            <div class="welcome-message text-center mb-4 d-flex flex-column justify-content-center align-items-center">
                <h3 class="mt-3 text-white text-shadow-strong">¡Bienvenido a tu panel!</h3>
                <p class="text-white text-shadow-strong">Aquí encontrarás información relevante y notificaciones.</p>
            </div>

            {{-- Sección: Mantenimientos Pendientes/En Progreso --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isUser()) {{-- Usar isAdmin() y isUser() --}}
            <div class="card mt-4 shadow-sm dashboard-info-card"> {{-- Añadida clase dashboard-info-card --}}
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Mantenimientos Pendientes/En Progreso</h4>
                </div>
                <div class="card-body d-flex flex-column"> {{-- Añadido d-flex flex-column --}}
                    @if($mantenimientosPendientes->isEmpty())
                        <div class="alert alert-info" role="alert">
                            No hay mantenimientos pendientes o en progreso en este momento.
                        </div>
                    @else
                        <div class="table-responsive flex-grow-1 overflow-y-auto"> {{-- Añadido flex-grow-1 overflow-y-auto --}}
                            <table class="table table-hover table-striped table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Tipo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mantenimientosPendientes as $mantenimiento)
                                    <tr>
                                        <td>{{ $mantenimiento->equipoTi->nombre_equipo ?? 'N/A' }}</td>
                                        <td>{{ $mantenimiento->tipo }}</td>
                                        <td>{{ $mantenimiento->fecha_inicio->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{
                                                $mantenimiento->estado == 'pendiente' ? 'bg-warning' :
                                                ($mantenimiento->estado == 'en_progreso' ? 'bg-info' : '')
                                            }}">
                                                {{ ucfirst($mantenimiento->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Solo permitir editar/eliminar si el usuario es admin --}}
                                            @if(auth()->user()->isAdmin())
                                            <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline-primary btn-sm">
                                Ver todos los mantenimientos <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            {{-- Fin de Sección: Mantenimientos Pendientes --}}

            {{-- NUEVA SECCIÓN: Insumos con Bajo Stock --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isUser()) {{-- Usar isAdmin() y isUser() --}}
            <div class="card mt-4 shadow-sm dashboard-info-card"> {{-- Añadida clase dashboard-info-card --}}
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Insumos con Bajo Stock</h4>
                </div>
                <div class="card-body d-flex flex-column"> {{-- Añadido d-flex flex-column --}}
                    @if($insumosBajoStock->isEmpty())
                        <div class="alert alert-success" role="alert">
                            ¡Todos los insumos tienen stock suficiente!
                        </div>
                    @else
                        <div class="table-responsive flex-grow-1 overflow-y-auto"> {{-- Añadido flex-grow-1 overflow-y-auto --}}
                            <table class="table table-hover table-striped table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Insumo</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($insumosBajoStock as $insumo)
                                    <tr>
                                        <td>{{ $insumo->nombre }}</td>
                                        <td><span class="badge bg-danger">{{ $insumo->stock }}</span></td>
                                        <td>{{ $insumo->stock_minimo }}</td>
                                        <td>
                                            <a href="{{ route('insumos-medicos.show', $insumo->id) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Solo permitir editar si el usuario es admin o bodega --}}
                                            @if(auth()->user()->isAdmin() || auth()->user()->role_id === 2) {{-- Asumiendo ID 2 para rol 'bodega' --}}
                                            <a href="{{ route('insumos-medicos.edit', $insumo->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('insumos-medicos.index') }}" class="btn btn-outline-danger btn-sm">
                                Ver todos los insumos <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            {{-- Fin de Sección: Insumos con Bajo Stock --}}

        </div>

        {{-- Panel de Notificaciones (Columna lateral) --}}
        <div class="col-lg-4 d-flex flex-column notification-panel-col"> {{-- CAMBIADO a col-lg-4 para más delgadez --}}
            <div class="card shadow-sm mb-4 flex-grow-1 notification-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Notificaciones Recientes</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    {{-- Sección de Últimos Mensajes de Chat --}}
                    <h6 class="text-primary"><i class="fas fa-comments"></i> Últimos Mensajes</h6>
                    <ul class="list-group list-group-flush mb-3 flex-grow-1 overflow-y-auto"> {{-- overflow-y-auto para scroll en lista --}}
                        @forelse($latestMessages as $message)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $message->user->name }}:</strong>
                                    <a href="{{ route('chat.show', $message->chat->id) }}" class="text-decoration-none text-body">
                                        {{ Str::limit($message->contenido, 30, '...') }} {{-- Asegura que se corta bien --}}
                                    </a>
                                    <small class="d-block text-muted">{{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/Santiago')->diffForHumans() }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No hay mensajes recientes.</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('chats.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-auto">Ver todos los chats <i class="fas fa-arrow-right"></i></a>

                    <hr class="my-3">

                    {{-- Sección de Últimos Cambios Realizados (Movimientos) --}}
                    <h6 class="text-success"><i class="fas fa-history"></i> Actividad Reciente</h6>
                    <ul class="list-group list-group-flush flex-grow-1 overflow-y-auto"> {{-- overflow-y-auto para scroll en lista --}}
                        @forelse($latestChanges as $movimiento)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ Str::limit($movimiento->summary, 40, '...') }} {{-- Ajusta el límite de caracteres --}}
                                    <small class="d-block text-muted">{{ \Carbon\Carbon::parse($movimiento->created_at)->setTimezone('America/Santiago')->diffForHumans() }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No hay actividad reciente registrada.</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('movimientos.index') }}" class="btn btn-sm btn-outline-success mt-3 w-100 mt-auto">Ver todos los movimientos <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- JavaScript específico para el home --}}
@endpush

@push('styles')
<style>
    /* Estilos específicos para la página de inicio (home) */

    /* La imagen de fondo se maneja en global-layout.css en el html/body */
    /* Aquí solo ajustamos el padding y márgenes de las columnas internas del home */

    /* Espaciado para las columnas principales dentro del row del home */
    .home-main-content-col,
    .notification-panel-col {
        padding: 20px; /* Padding para el contenido dentro de las columnas */
        box-sizing: border-box;
    }

    /* Estilos para el mensaje de bienvenida y su contenedor */
    .welcome-message {
        background-color: rgba(0, 0, 0, 0.4);
        padding: 30px;
        border-radius: 8px;
        color: white;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        /* Ajuste para que sea un rectángulo horizontal */
        height: auto; /* Permitir que el contenido defina la altura mínima */
        min-height: 150px; /* Altura mínima para un aspecto rectangular */
        flex-grow: 0; /* No crecerá para ocupar espacio extra */
    }
    .welcome-message h3, .welcome-message p {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    }

    /* Estilos para el panel de notificaciones (columna lateral) */
    .notification-card {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 10px;
        flex-grow: 1; /* Permitirá que esta tarjeta crezca para ocupar espacio */
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .notification-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .notification-card .list-group {
        flex-grow: 1;
        overflow-y: auto;
        margin-bottom: 15px;
    }

    .notification-card .list-group-item {
        background-color: transparent;
        border-color: rgba(0,0,0,0.05);
    }

    /* Estilos para las nuevas tarjetas de información (Mantenimientos e Insumos) */
    .dashboard-info-card {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        /* Ajuste para hacerlas más altas */
        min-height: 300px; /* Altura mínima para estas tarjetas */
        flex-grow: 1; /* Permitir que crezcan si hay más contenido */
    }

    .dashboard-info-card .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .dashboard-info-card .table-responsive {
        flex-grow: 1;
        overflow-y: auto;
    }


    /* Estilos de notificaciones de Bootstrap para esta tarjeta */
    .notification-card .card-header.bg-info {
        background-color: #17a2b8 !important;
    }
    .notification-card .card-header.bg-success {
        background-color: #28a745 !important;
    }
    .notification-card .list-group-item strong {
        color: #343a40;
    }
    .notification-card .list-group-item a {
        color: #007bff;
        font-weight: 500;
    }

    /* Responsive Media Queries (específicas del home) */
    @media (max-width: 991.98px) {
        .home-main-content-col,
        .notification-panel-col {
            width: 100%;
            padding: 15px;
            height: auto;
        }
        .welcome-message {
            height: auto;
            min-height: 120px; /* Ajuste para móviles */
        }
        .notification-card,
        .dashboard-info-card {
            height: auto;
            min-height: 250px; /* Ajuste para móviles */
        }
    }
</style>
@endpush
