@extends('layouts.app')

@section('title', 'Historial de Movimientos')

@section('content')
<div class="container">
    <div class="card shadow-sm"> {{-- Añadido shadow-sm para consistencia --}}
        <div class="card-header bg-primary text-white"> {{-- Estilos de header consistentes --}}
            <h1 class="card-title mb-0">Historial de Movimientos</h1>
            {{-- Puedes añadir un botón para crear un nuevo movimiento si es necesario y si el rol lo permite --}}
            {{-- <a href="{{ route('movimientos.create') }}" class="btn btn-create">
                <i class="fas fa-plus"></i> Registrar Nuevo Movimiento
            </a> --}}
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULARIO DE FILTROS --}}
            <h3 class="mb-3">Filtros de Movimientos</h3>
            <form action="{{ route('movimientos.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end"> {{-- Fila principal para filtros y botones --}}

                    {{-- Columna para los campos de filtro --}}
                    <div class="col-12 col-md-9 col-lg-9">
                        <div class="row g-3"> {{-- Fila anidada para filtros individuales --}}
                            <div class="col-12 col-md-4">
                                <label for="tipo" class="form-label">Tipo de Movimiento:</label>
                                <select id="tipo" name="tipo" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    {{-- Iterar sobre los tipos de movimiento que pasa el controlador --}}
                                    @foreach($tiposMovimiento as $tipoOption)
                                        <option value="{{ $tipoOption }}" {{ request('tipo') == $tipoOption ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $tipoOption)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="user_id" class="form-label">Usuario:</label>
                                <select id="user_id" name="user_id" class="form-select">
                                    <option value="">Todos los usuarios</option>
                                    {{-- Iterar sobre los usuarios que pasa el controlador --}}
                                    @foreach($usuarios as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="equipo_ti_id" class="form-label">Equipo TI:</label>
                                <select id="equipo_ti_id" name="equipo_ti_id" class="form-select">
                                    <option value="">Todos los equipos</option>
                                    {{-- Iterar sobre los equipos que pasa el controlador --}}
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo->id }}" {{ request('equipo_ti_id') == $equipo->id ? 'selected' : '' }}>
                                            {{ $equipo->nombre_equipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Columna para los botones de acción (Buscar, Limpiar) --}}
                    <div class="col-12 col-md-3 col-lg-3 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
            {{-- FIN FORMULARIO DE FILTROS --}}


            <div class="table-responsive mt-4"> {{-- Margen superior para separar de los filtros --}}
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th> {{-- CAMBIADO: Antes era ID, para hacer más legible por fecha --}}
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Usuario</th>
                            <th>Ítem Afectado</th>
                            <th>IP</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movimientos as $movimiento)
                            <tr>
                                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{-- Usamos $movimiento->tipo (ya corregido en modelo y controlador) --}}
                                    <span class="badge {{
                                        $movimiento->tipo === 'entrada' ? 'bg-success' :
                                        ($movimiento->tipo === 'salida' ? 'bg-danger' :
                                        ($movimiento->tipo === 'edicion_equipo' ? 'bg-info text-dark' :
                                        ($movimiento->tipo === 'baja_equipo' ? 'bg-dark' :
                                        'bg-secondary')))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($movimiento->descripcion, 70, '...') }}</td> {{-- Limitar la descripción --}}
                                <td>{{ $movimiento->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($movimiento->equipoTi)
                                        <span class="badge bg-warning text-dark">Equipo: {{ $movimiento->equipoTi->nombre_equipo }}</span>
                                    @elseif ($movimiento->insumoMedico)
                                        <span class="badge bg-primary">Insumo: {{ $movimiento->insumoMedico->nombre }}</span> {{-- Asumo que el campo de nombre del insumo es 'nombre' --}}
                                    @else
                                        N/A
                                    @endif
                                    <br>
                                    <small class="text-muted">
                                        Tabla: {{ $movimiento->tabla_afectada ?? 'N/A' }}, ID: {{ $movimiento->id_afectada ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>{{ $movimiento->ip_address ?? 'N/A' }}</td>
                                <td class="actions-buttons d-flex gap-2"> {{-- Alineación de botón --}}
                                    <a href="{{ route('movimientos.show', $movimiento->id) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay movimientos registrados que coincidan con los criterios de búsqueda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $movimientos->links() }}
            </div>

            <div class="button-container d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-secondary"> {{-- Botón secundario para "Volver" --}}
                    <i class="fas fa-arrow-circle-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush
