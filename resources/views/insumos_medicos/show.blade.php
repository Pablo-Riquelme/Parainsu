{{-- resources/views/insumos_medicos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalles del Insumo Médico: ' . $insumoMedico->nombre)

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Detalles del Insumo Médico</h1>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Nombre:</strong>
                    <p class="form-control-plaintext">{{ $insumoMedico->nombre }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Unidad de Medida:</strong>
                    <p class="form-control-plaintext">{{ $insumoMedico->unidad_medida }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <strong>Descripción:</strong>
                    <p class="form-control-plaintext">{{ $insumoMedico->descripcion ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Stock Actual:</strong>
                    <p class="form-control-plaintext">
                        <span class="badge {{ $insumoMedico->stock <= $insumoMedico->stock_minimo ? 'bg-danger' : 'bg-success' }}">
                            {{ $insumoMedico->stock }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <strong>Stock Mínimo:</strong>
                    <p class="form-control-plaintext">{{ $insumoMedico->stock_minimo ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Precio Unitario:</strong>
                    <p class="form-control-plaintext">${{ number_format($insumoMedico->precio, 2) }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Proveedor:</strong>
                    <p class="form-control-plaintext">{{ $insumoMedico->proveedor ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Fecha de Creación:</strong>
                    +
                    <p class="form-control-plaintext">{{ $insumoMedico->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-start gap-2">
                <a href="{{ route('insumos-medicos.edit', $insumoMedico) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar Insumo
                </a>
                <a href="{{ route('insumos-medicos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-circle-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection