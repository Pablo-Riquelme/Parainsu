@extends('layouts.app')

@section('title', 'Gestión de Mantenimientos')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0 text-white"><i class="fas fa-tools"></i> Mantenimientos Programados</h2>
            <a href="{{ route('mantenimientos.create') }}" class="btn btn-light btn-sm text-primary">
                <i class="fas fa-plus-circle"></i> Programar Nuevo Mantenimiento
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($mantenimientos->isEmpty())
                <div class="alert alert-info" role="alert">
                    No hay mantenimientos programados aún.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Equipo TI</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mantenimientos as $mantenimiento)
                                <tr>
                                    <td>{{ $mantenimiento->id }}</td>
                                    <td>{{ $mantenimiento->equipoTi->nombre_equipo ?? 'N/A' }}</td>
                                    <td>{{ $mantenimiento->fecha_inicio->format('d/m/Y H:i') }}</td>
                                    <td>{{ $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y H:i') : 'Pendiente' }}</td>
                                    <td>{{ $mantenimiento->tipo }}</td>
                                    <td>
                                        <span class="badge {{
                                            $mantenimiento->estado == 'completado' ? 'bg-success' :
                                            ($mantenimiento->estado == 'en_progreso' ? 'bg-info' :
                                            ($mantenimiento->estado == 'cancelado' ? 'bg-danger' : 'bg-warning'))
                                        }}">
                                            {{ ucfirst($mantenimiento->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este mantenimiento?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Volver al Menú Principal
    </a>
</div>
@endsection
