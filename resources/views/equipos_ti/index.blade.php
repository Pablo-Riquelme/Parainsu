@extends('layouts.app')

@section('title', 'Gestión de Equipos TI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Equipos de TI</h1>
            <a href="{{ route('equipos-ti.create') }}" class="btn btn-create">
                <i class="fas fa-plus"></i> Añadir Nuevo Equipo
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULARIO DE BÚSQUEDA Y FILTRADO --}}
            <form action="{{ route('equipos-ti.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-9 col-lg-9">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="search" class="form-label">Buscar:</label>
                                <input type="text" name="search" id="search"
                                       class="form-control"
                                       placeholder="Nombre, Ubicación, Serie, Modelo, Marca"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="estado_filtro" class="form-label">Filtrar por Estado:</label>
                                <select name="estado_filtro" id="estado_filtro" class="form-select">
                                    <option value="">Todos los Estados</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->value }}"
                                            {{ request('estado_filtro') == $estado->value ? 'selected' : '' }}>
                                            {{ $estado->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="usuario_filtro" class="form-label">Filtrar por Usuario:</label>
                                <select name="usuario_filtro" id="usuario_filtro" class="form-select">
                                    <option value="">Todos los Usuarios</option>
                                    <option value="null" {{ request('usuario_filtro') == 'null' ? 'selected' : '' }}>
                                        Sin Asignar
                                    </option>
                                    @foreach ($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}"
                                            {{ request('usuario_filtro') == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('equipos-ti.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
            {{-- FIN FORMULARIO DE BÚSQUEDA Y FILTRADO --}}

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Usuario Asignado</th>
                            <th>Número de Serie</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Fecha Adquisición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($equipos as $equipo)
                            <tr>
                                <td>{{ $equipo->id }}</td>
                                <td>{{ $equipo->nombre_equipo }}</td>
                                <td>{{ $equipo->ubicacion }}</td>
                                <td>
                                    {{ $equipo->estado->label() ?? $equipo->estado }}
                                </td>
                                <td>{{ $equipo->usuario->name ?? 'N/A' }}</td>
                                <td>{{ $equipo->numero_serie ?? 'N/A' }}</td>
                                <td>{{ $equipo->modelo ?? 'N/A' }}</td>
                                <td>{{ $equipo->marca ?? 'N/A' }}</td>
                                <td>{{ $equipo->fecha_adquisicion ? $equipo->fecha_adquisicion->format('d/m/Y') : 'N/A' }}</td>
                                <td class="actions-buttons d-flex gap-2">
                                    <a href="{{ route('equipos-ti.show', $equipo) }}" class="btn btn-primary btn-sm" title="Ver Detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('equipos-ti.edit', $equipo) }}" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('equipos-ti.destroy', $equipo) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este equipo?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No hay equipos de TI registrados que coincidan con los criterios de búsqueda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $equipos->links() }}
            </div>

            <div class="button-container d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-home">
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