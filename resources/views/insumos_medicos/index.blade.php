{{-- resources/views/insumos_medicos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Insumos Médicos')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Insumos Médicos</h1>
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
            <form action="{{ route('insumos-medicos.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-9 col-lg-9">
                        <div class="row g-3">
                            <div class="col-12 col-md-12">
                                <label for="nombre_filtro" class="form-label">Buscar por dato:</label>
                                <input type="text" name="nombre_filtro" id="nombre_filtro"
                                       class="form-control"
                                       placeholder="Dato relacionado con el insumo médico"
                                       value="{{ request('nombre_filtro') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3 d-grid gap-2">
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
                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download"></i> Exportar
                </a>
            </div>

            {{-- Tabla de Insumos Médicos --}}
            <div class="table-responsive">
                <table class="table table-hover">
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
                                    <a href="{{ route('insumos-medicos.show', $insumo) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('insumos-medicos.edit', $insumo) }}" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('insumos-medicos.destroy', $insumo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete delete-alert"> {{-- Añadido delete-alert para SweetAlert2 --}}
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
    <div class="modal-dialog modal-dialog-centered"> {{-- Añadido modal-dialog-centered para centrar verticalmente --}}
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"> {{-- Estilo para el header del modal --}}
                <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-download"></i> Exportar Datos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Selecciona el formato de exportación deseado:
            </div>
            <div class="modal-footer d-flex justify-content-center">
                {{-- Botón para exportar a Excel --}}
                <a href="{{ route('insumos-medicos.export.excel', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </a>
                {{-- Botón para exportar a PDF --}}
                <a href="{{ route('insumos-medicos.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Exportar a PDF
                </a>
                {{-- Botón para exportar como Imagen (se manejará con JavaScript) --}}
                <button type="button" class="btn btn-warning" id="exportImageBtn">
                    <i class="fas fa-file-image"></i> Exportar a Imagen
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert2 para confirmación de eliminación (si ya lo tienes, solo verifica la clase)
    document.querySelectorAll('.delete-alert').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });

    // Lógica para exportar como IMAGEN (usando html2canvas)
    const exportImageBtn = document.getElementById('exportImageBtn');
    if (exportImageBtn) {
        exportImageBtn.addEventListener('click', function() {
            // Ocultar elementos no deseados antes de la captura (ej. acciones de la tabla, botones de paginación)
            const actionsColumn = document.querySelectorAll('.actions-buttons');
            actionsColumn.forEach(col => col.style.display = 'none');

            const pagination = document.querySelector('.pagination-container');
            if (pagination) pagination.style.display = 'none';

            const addInsumoBtn = document.querySelector('a[href="{{ route('insumos-medicos.create') }}"]');
            if (addInsumoBtn) addInsumoBtn.style.display = 'none';

            const exportBtn = document.querySelector('.d-flex.justify-content-end.mb-3 .btn-info');
            if (exportBtn) exportBtn.style.display = 'none';

            const returnHomeBtn = document.querySelector('.button-container');
            if (returnHomeBtn) returnHomeBtn.style.display = 'none';

            // Seleccionar el contenido de la tabla para capturar
            const tableToCapture = document.querySelector('.card-body .table-responsive'); // Captura solo la tabla y su responsividad

            if (tableToCapture) {
                html2canvas(tableToCapture, {
                    scale: 2, // Aumenta la resolución para mejor calidad
                    useCORS: true // Importante si tienes imágenes de fuentes externas
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.download = 'insumos_medicos_tabla.png';
                    link.href = imgData;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Restaurar la visibilidad de los elementos ocultos
                    actionsColumn.forEach(col => col.style.display = ''); // o 'table-cell' si es td
                    if (pagination) pagination.style.display = '';
                    if (addInsumoBtn) addInsumoBtn.style.display = '';
                    if (exportBtn) exportBtn.style.display = '';
                    if (returnHomeBtn) returnHomeBtn.style.display = '';

                    // Cerrar el modal después de la exportación
                    const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
                    if (exportModal) {
                        exportModal.hide();
                    }
                    Swal.fire('Exportado', 'Los insumos se han exportado como imagen (PNG).', 'success');
                }).catch(error => {
                    console.error('Error al generar la imagen:', error);
                    Swal.fire('Error', 'No se pudo exportar como imagen.', 'error');
                    // Asegurarse de restaurar visibilidad incluso en error
                    actionsColumn.forEach(col => col.style.display = '');
                    if (pagination) pagination.style.display = '';
                    if (addInsumoBtn) addInsumoBtn.style.display = '';
                    if (exportBtn) exportBtn.style.display = '';
                    if (returnHomeBtn) returnHomeBtn.style.display = '';
                });
            } else {
                Swal.fire('Error', 'No se encontró la tabla para exportar.', 'error');
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Estilos específicos para la gestión de insumos médicos */

    /* Card header para el título y botón de añadir */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #348FFF; /* Color de fondo azul */
        color: white; /* Color de texto blanco */
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 0.5rem; /* Asegura bordes redondeados consistentes */
        border-top-right-radius: 0.5rem;
    }

    .card-header .card-title {
        margin-bottom: 0;
        font-size: 1.75rem; /* Ajusta el tamaño del título si es necesario */
        font-weight: bold;
    }

    /* Botón "Añadir Nuevo Insumo" en el header */
    .btn-create {
        background-color: #28a745; /* Verde Bootstrap */
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
    }

    .btn-create:hover {
        background-color: #218838; /* Verde más oscuro al pasar el ratón */
        color: white;
    }

    /* Ajustes generales para la tabla */
    .table-responsive {
        margin-top: 1rem;
    }

    .table thead th {
        background-color: #f8f9fa; /* Color de fondo para los encabezados de tabla */
        color: #495057; /* Color de texto para los encabezados */
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
        text-align: center;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    /* Estilos para los botones de acción en la tabla */
    .actions-buttons .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .btn-edit {
        background-color: #ffc107; /* Amarillo Bootstrap */
        color: #212529; /* Texto oscuro para contraste */
        border: none;
    }

    .btn-edit:hover {
        background-color: #e0a800; /* Amarillo más oscuro */
        color: #212529;
    }

    .btn-delete {
        background-color: #dc3545; /* Rojo Bootstrap */
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background-color: #c82333; /* Rojo más oscuro */
        color: white;
    }

    /* Paginación */
    .pagination-container {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    /* Botón "Volver al Dashboard" */
    .button-container {
        margin-top: 1.5rem;
    }

    .btn-home {
        background-color: #6c757d; /* Gris de Bootstrap (secondary) */
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
    }

    .btn-home:hover {
        background-color: #5a6268; /* Gris más oscuro */
        color: white;
    }

    /* Asegurar que el modal de exportación se muestre correctamente */
    #exportModal .modal-header.bg-primary {
        background-color: #348FFF !important; /* Usa !important para asegurar la sobrescritura */
    }

    #exportModal .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%); /* Hace la X blanca */
    }

</style>
@endpush
```
---

