{{-- resources/views/insumos_medicos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Insumo Médico: ' . $insumoMedico->nombre)

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Editar Insumo Médico: {{ $insumoMedico->nombre }}</h1>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('insumos-medicos.update', [$insumoMedico]) }}" method="POST">
                @csrf
                @method('PUT') {{-- Esto simula un PUT request, necesario para el método update --}}

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre del Insumo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $insumoMedico->nombre) }}" required>
                        @error('nombre')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" value="{{ old('unidad_medida', $insumoMedico->unidad_medida) }}" placeholder="Ej: unidades, ml, g" required>
                        @error('unidad_medida')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $insumoMedico->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $insumoMedico->stock) }}" min="0" required>
                        @error('stock')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $insumoMedico->stock_minimo) }}" min="0">
                        @error('stock_minimo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio Unitario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="{{ old('precio', $insumoMedico->precio) }}" min="0" required>
                        </div>
                        @error('precio')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="proveedor" class="form-label">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor" name="proveedor" value="{{ old('proveedor', $insumoMedico->proveedor) }}" placeholder="Nombre del proveedor">
                        @error('proveedor')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Actualizar Insumo
                    </button>
                    <a href="{{ route('insumos-medicos.index', $insumoMedico) }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection