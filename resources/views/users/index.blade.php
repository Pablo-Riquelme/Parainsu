@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Usuarios</h1>
            <a href="{{ route('users.create') }}" class="btn btn-create">
                <i class="fas fa-plus"></i> Añadir Nuevo Usuario
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULARIO DE BÚSQUEDA Y FILTRADO --}}
            <form action="{{ route('users.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end"> {{-- Una sola fila para filtros y botones --}}
                    <div class="col-12 col-md-9 col-lg-9"> {{-- Columna para los campos de filtro --}}
                        <div class="row g-3"> {{-- Fila anidada para los filtros individuales --}}
                            <div class="col-12 col-md-6"> {{-- Columna para el input de búsqueda --}}
                                <label for="search" class="form-label">Buscar:</label>
                                <input type="text" name="search" id="search"
                                       class="form-control"
                                       placeholder="Nombre o Correo Electrónico"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-12 col-md-6"> {{-- Columna para el select de rol --}}
                                <label for="role_filtro" class="form-label">Filtrar por Rol:</label>
                                <select name="role_filtro" id="role_filtro" class="form-select">
                                    <option value="">Todos los Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ request('role_filtro') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3 d-grid gap-2"> {{-- Columna para los botones de acción --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
            {{-- FIN FORMULARIO DE BÚSQUEDA Y FILTRADO --}}

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->name ?? 'N/A' }}</td>
                                <td class="actions-buttons d-flex gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay usuarios registrados que coincidan con los criterios de búsqueda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $users->links() }}
            </div>

            <div class="button-container d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-home">
                    <i class="fas fa-arrow-circle-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')     
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush