@extends('layouts.app')

@section('title', 'Detalles del Equipo TI: ' . $equipoTI->nombre_equipo)

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Detalles del Equipo TI</h1>
        </div>
        <div class="card-body">
            <div class="details-section mb-3 d-flex justify-content-center flex-column align-items-center">

                <p class="detail-line mb-2">
                    <strong class="detail-label">Nombre:</strong> {{ $equipoTI->nombre_equipo }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Tipo de Equipo:</strong> {{ $equipoTI->tipo_equipo ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Marca:</strong> {{ $equipoTI->marca ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Modelo:</strong> {{ $equipoTI->modelo ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Número de Serie:</strong> {{ $equipoTI->numero_serie ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Estado:</strong>
                    <span class="badge
                        {{-- Usamos $equipoTI->estado->value para comparar con el valor del Enum --}}
                        @if(($equipoTI->estado->value ?? null) == 'en_uso')
                            bg-success
                        @elseif(($equipoTI->estado->value ?? null) == 'en_desuso')
                            bg-danger
                        @elseif(($equipoTI->estado->value ?? null) == 'en_reparacion')
                            bg-warning text-dark
                        @elseif(($equipoTI->estado->value ?? null) == 'disponible')
                            bg-info {{-- Usamos bg-info para un azul claro, si prefieres el azul oscuro de Bootstrap, usa bg-primary --}}
                        @else
                            bg-secondary {{-- Si el estado no coincide o es nulo --}}
                        @endif
                    ">
                        {{-- Mostramos el label del Enum si existe, o el valor original, o 'N/A' --}}
                        @if(($equipoTI->estado->value ?? null) == 'en_uso')
                            EN USO
                        @elseif(($equipoTI->estado->value ?? null) == 'en_desuso')
                            EN DESUSO
                        @elseif(($equipoTI->estado->value ?? null) == 'en_reparacion')
                            EN REPARACIÓN
                        @elseif(($equipoTI->estado->value ?? null) == 'disponible')
                            DISPONIBLE
                        @else
                            {{ $equipoTI->estado->label() ?? $equipoTI->estado->value ?? 'N/A' }}
                        @endif
                    </span>
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Ubicación:</strong> {{ $equipoTI->ubicacion ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Responsable:</strong> {{ $equipoTI->responsable ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Descripción:</strong> {{ $equipoTI->descripcion ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Fecha Adquisición:</strong> {{ $equipoTI->fecha_adquisicion?->format('d/m/Y') ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Fecha de Creación:</strong> {{ $equipoTI->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                </p>

            </div> {{-- Fin de details-section --}}

            <hr class="my-4">

            {{-- Botones centrados --}}
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('equipos-ti.edit', $equipoTI) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar Equipo
                </a>
                <a href="{{ route('equipos-ti.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-circle-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection