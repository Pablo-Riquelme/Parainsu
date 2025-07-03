@extends('layouts.app')

@section('title', 'Detalles del Mantenimiento')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0"><i class="fas fa-info-circle"></i> Detalles del Mantenimiento</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>ID:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Equipo TI:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->equipoTi->nombre_equipo ?? 'N/A' }} ({{ $mantenimiento->equipoTi->numero_serie ?? 'N/A' }})</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Fecha de Inicio:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->fecha_inicio->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Fecha de Fin:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y H:i') : 'Pendiente' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->tipo }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Estado:</strong></div>
                <div class="col-md-8">
                    <span class="badge {{
                        $mantenimiento->estado == 'completado' ? 'bg-success' :
                        ($mantenimiento->estado == 'en_progreso' ? 'bg-info' :
                        ($mantenimiento->estado == 'cancelado' ? 'bg-danger' : 'bg-warning'))
                    }}">
                        {{ ucfirst($mantenimiento->estado) }}
                    </span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Descripción:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->descripcion ?? 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Observaciones:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->observaciones ?? 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Creado el:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Última Actualización:</strong></div>
                <div class="col-md-8">{{ $mantenimiento->updated_at->format('d/m/Y H:i') }}</div>
            </div>
            <hr>
            <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este mantenimiento?')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
