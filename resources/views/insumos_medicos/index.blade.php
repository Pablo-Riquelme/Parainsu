{{-- resources/views/insumos_medicos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Insumos Médicos')

@section('content')
<div class="container"> {{-- Usamos 'container' para el mismo ancho centralizado --}}
    <div class="card"> {{-- Una única tarjeta que envuelve todo el contenido --}}
        <div class="card-header">
            <h1 class="card-title">Insumos Médicos</h1> {{-- Título dentro del header de la tarjeta --}}
            <a href="{{ route('insumos-medicos.create') }}" class="btn btn-create">
                <i class="fas fa-plus"></i> Añadir Nuevo Insumo
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULARIO DE BÚSQUEDA Y FILTRADO --}}
            {{-- No necesitamos una tarjeta separada para los filtros, el card-body principal es suficiente --}}
            <form action="{{ route('insumos-medicos.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-9 col-lg-9"> {{-- Columna para los campos de filtro --}}
                        <div class="row g-3">
                            <div class="col-12 col-md-12"> {{-- Columna para el input de búsqueda --}}
                                <label for="nombre_filtro" class="form-label">Buscar por dato:</label>
                                <input type="text" name="nombre_filtro" id="nombre_filtro"
                                       class="form-control"
                                       placeholder="Dato relacionado con el insumo médico"
                                       value="{{ request('nombre_filtro') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3 d-grid gap-2"> {{-- Columna para los botones de acción --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('insumos-medicos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
            {{-- FIN FORMULARIO DE BÚSQUEDA Y FILTRADO --}}

            {{-- Botones de Acción Global (dentro del card-body, antes de la tabla) --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('insumos-medicos.create') }}" class="btn btn-success me-2">
                    <i class="fas fa-plus-circle"></i> Agregar Insumo Médico
                </a>
                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download"></i> Exportar
                </a>
            </div>

            {{-- Tabla de Insumos Médicos --}}
            <div class="table-responsive">
                <table class="table table-hover"> {{-- Eliminado table-striped para que sea igual a equipos_ti --}}
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Stock</th>
                            <th>Stock Mínimo</th>
                            <th>Precio</th>
                            <th>Proveedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($insumosMedicos as $insumo)
                            <tr>
                                <td>{{ $insumo->id }}</td>
                                <td>{{ $insumo->nombre }}</td>
                                <td>{{ Str::limit($insumo->descripcion, 50) ?? 'N/A' }}</td>
                                <td>{{ $insumo->unidad_medida }}</td>
                                <td>
                                    <span class="badge {{ $insumo->stock <= $insumo->stock_minimo ? 'bg-danger' : 'bg-success' }}">
                                        {{ $insumo->stock }}
                                    </span>
                                </td>
                                <td>{{ $insumo->stock_minimo ?? 'N/A' }}</td>
                                <td>{{ $insumo->precio ? '$' . number_format($insumo->precio, 2) : 'N/A' }}</td>
                                <td>{{ $insumo->proveedor ?? 'N/A' }}</td>
                                <td class="actions-buttons d-flex gap-2">
                                    <a href="{{ route('insumos-medicos.show', $insumo) }}" class="btn btn-edit">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('insumos-medicos.edit', $insumo) }}" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('insumos-medicos.destroy', $insumo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este insumo?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay insumos médicos registrados que coincidan con los criterios de búsqueda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $insumosMedicos->links() }}
            </div>

            <div class="button-container d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-home">
                    <i class="fas fa-arrow-circle-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE EXPORTACIÓN (fuera del card principal, pero dentro del container) --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Exportar Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Selecciona el formato de exportación:
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Exportar a Excel</button>
                <button type="button" class="btn btn-info">Exportar a PDF</button>
            </div>
        </div>
    </div>
</div>

@endsection