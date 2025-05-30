@extends('layouts.app')

@section('title', 'Detalles del Insumo Médico: ' . $insumoMedico->nombre)

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Detalles del Insumo Médico</h1>
        </div>
        <div class="card-body">
            <div class="details-section mb-3 d-flex justify-content-center flex-column align-items-center"> 

                <p class="detail-line mb-2">
                    <strong class="detail-label">Nombre:</strong> {{ $insumoMedico->nombre }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Unidad de Medida:</strong> {{ $insumoMedico->unidad_medida }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Descripción:</strong> {{ $insumoMedico->descripcion ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Stock Actual:</strong>
                    <span class="badge {{ $insumoMedico->stock <= ($insumoMedico->stock_minimo ?? 0) ? 'bg-danger' : 'bg-success' }}">
                        {{ $insumoMedico->stock }}
                    </span>
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Stock Mínimo:</strong> {{ $insumoMedico->stock_minimo ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Precio Unitario:</strong> ${{ number_format($insumoMedico->precio, 2) }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Proveedor:</strong> {{ $insumoMedico->proveedor ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Fecha de Creación:</strong> {{ $insumoMedico->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                </p>

            </div> {{-- Fin de details-section --}}

            <hr class="my-4">

            {{-- CAMBIO AQUÍ: justify-content-start a justify-content-center --}}
            <div class="d-flex justify-content-center gap-2">
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