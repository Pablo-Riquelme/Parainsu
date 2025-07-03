@extends('layouts.app')

@section('title', 'Editar Mantenimiento')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0"><i class="fas fa-edit"></i> Editar Mantenimiento</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('mantenimientos.update', $mantenimiento->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="equipo_ti_id" class="form-label">Equipo TI</label>
                    <select class="form-select" id="equipo_ti_id" name="equipo_ti_id" required>
                        <option value="">Selecciona un equipo</option>
                        @foreach ($equiposTi as $equipo)
                            <option value="{{ $equipo->id }}" {{ old('equipo_ti_id', $mantenimiento->equipo_ti_id) == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre_equipo }} ({{ $equipo->numero_serie }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha y Hora de Inicio</label>
                    <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha y Hora de Fin (Opcional)</label>
                    <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Mantenimiento</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" value="{{ old('tipo', $mantenimiento->tipo) }}" placeholder="Ej: Preventivo, Correctivo" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $mantenimiento->observaciones) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="pendiente" {{ old('estado', $mantenimiento->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ old('estado', $mantenimiento->estado) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completado" {{ old('estado', $mantenimiento->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ old('estado', $mantenimiento->estado) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar Mantenimiento</button>
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times-circle"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
