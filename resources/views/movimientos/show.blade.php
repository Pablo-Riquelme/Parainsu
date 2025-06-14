@extends('layouts.app')

@section('title', 'Detalles del Movimiento: ' . $movimiento->id)

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Detalles del Movimiento #{{ $movimiento->id }}</h1>
        </div>
        <div class="card-body">
            <div class="details-section mb-3 d-flex justify-content-center flex-column align-items-center">

                <p class="detail-line mb-2">
                    <strong class="detail-label">ID Movimiento:</strong> {{ $movimiento->id }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Cantidad:</strong> {{ $movimiento->cantidad }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Descripción:</strong> {{ $movimiento->descripcion }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Realizado por:</strong> {{ $movimiento->user->name ?? 'Usuario Desconocido' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Fecha/Hora:</strong> {{ $movimiento->created_at->format('d/m/Y H:i:s') }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">IP:</strong> {{ $movimiento->ip_address ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">Tabla Afectada:</strong> {{ $movimiento->tabla_afectada ?? 'N/A' }}
                </p>
                <p class="detail-line mb-2">
                    <strong class="detail-label">ID Afectado:</strong> {{ $movimiento->id_afectada ?? 'N/A' }}
                </p>

                @if ($movimiento->equipoTi)
                    <p class="detail-line mb-2">
                        <strong class="detail-label">Equipo TI:</strong>
                        <a href="{{ route('equipos-ti.show', $movimiento->equipoTi->id) }}" class="text-primary text-decoration-none">
                            {{ $movimiento->equipoTi->nombre_equipo }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </p>
                @endif

                @if ($movimiento->insumoMedico)
                    <p class="detail-line mb-2">
                        <strong class="detail-label">Insumo Médico:</strong>
                        <a href="{{ route('insumos-medicos.show', $movimiento->insumoMedico->id) }}" class="text-primary text-decoration-none">
                            {{ $movimiento->insumoMedico->nombre }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </p>
                @endif

            </div> {{-- Fin de details-section --}}

            <hr class="my-4">

            <div class="mt-4">
                <h4 class="card-title mb-2">Datos Antes (JSON)</h4>
                @if ($movimiento->datos_antes)
                    <pre class="bg-light p-3 rounded overflow-auto text-monospace small" style="max-height: 300px;"><code>{{ json_encode($movimiento->datos_antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @else
                    <p class="text-muted">No hay datos antes registrados.</p>
                @endif
            </div>

            <div class="mt-4">
                <h4 class="card-title mb-2">Datos Después (JSON)</h4>
                @if ($movimiento->datos_despues)
                    <pre class="bg-light p-3 rounded overflow-auto text-monospace small" style="max-height: 300px;"><code>{{ json_encode($movimiento->datos_despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @else
                    <p class="text-muted">No hay datos después registrados.</p>
                @endif
            </div>

            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-circle-left"></i> Volver al Historial
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush
