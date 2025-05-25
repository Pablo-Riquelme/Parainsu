@extends('layouts.app')

@section('title', 'Editar Equipo TI')

@section('content')
{{-- Incluimos creador.css si tiene estilos específicos para formularios --}}
<link href="{{ asset('css/creador.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Usamos la columna de Bootstrap --}}
            <div class="card">
                <div class="card-header">{{ __('Editar Equipo TI') }}</div>

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

                    <form action="{{ route('equipos-ti.update', $equipoTI) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre_equipo" class="form-label">{{ __('Nombre del Equipo') }}</label>
                            <input id="nombre_equipo" type="text"
                                   class="form-control @error('nombre_equipo') is-invalid @enderror"
                                   name="nombre_equipo" value="{{ old('nombre_equipo', $equipoTI->nombre_equipo) }}" required autocomplete="off" autofocus>
                            @error('nombre_equipo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">{{ __('Ubicación') }}</label>
                            <input id="ubicacion" type="text"
                                   class="form-control @error('ubicacion') is-invalid @enderror"
                                   name="ubicacion" value="{{ old('ubicacion', $equipoTI->ubicacion) }}" required autocomplete="off">
                            @error('ubicacion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">{{ __('Estado') }}</label>
                            <select id="estado" name="estado"
                                    class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="">{{ __('Seleccione un estado') }}</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->value }}"
                                        {{ old('estado', $equipoTI->estado->value ?? '') == $estado->value ? 'selected' : '' }}>
                                        {{ $estado->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="usuario_asignado_id" class="form-label">{{ __('Usuario Asignado') }} (Opcional)</label>
                            <select id="usuario_asignado_id" name="usuario_asignado_id"
                                    class="form-select @error('usuario_asignado_id') is-invalid @enderror">
                                <option value="">{{ __('Ninguno') }}</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}"
                                        {{ old('usuario_asignado_id', $equipoTI->usuario_asignado_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('usuario_asignado_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="numero_serie" class="form-label">{{ __('Número de Serie') }} (Opcional)</label>
                            <input id="numero_serie" type="text"
                                   class="form-control @error('numero_serie') is-invalid @enderror"
                                   name="numero_serie" value="{{ old('numero_serie', $equipoTI->numero_serie) }}" autocomplete="off">
                            @error('numero_serie')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">{{ __('Modelo') }} (Opcional)</label>
                            <input id="modelo" type="text"
                                   class="form-control @error('modelo') is-invalid @enderror"
                                   name="modelo" value="{{ old('modelo', $equipoTI->modelo) }}" autocomplete="off">
                            @error('modelo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="marca" class="form-label">{{ __('Marca') }} (Opcional)</label>
                            <input id="marca" type="text"
                                   class="form-control @error('marca') is-invalid @enderror"
                                   name="marca" value="{{ old('marca', $equipoTI->marca) }}" autocomplete="off">
                            @error('marca')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_adquisicion" class="form-label">{{ __('Fecha de Adquisición') }} (Opcional)</label>
                            <input id="fecha_adquisicion" type="date"
                                   class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                                   name="fecha_adquisicion" value="{{ old('fecha_adquisicion', $equipoTI->fecha_adquisicion ? $equipoTI->fecha_adquisicion->format('Y-m-d') : '') }}">
                            @error('fecha_adquisicion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">{{ __('Descripción') }} (Opcional)</label>
                            <textarea id="descripcion" name="descripcion" rows="4"
                                      class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $equipoTI->descripcion) }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Actualizar Equipo') }}
                            </button>
                            <a href="{{ route('equipos-ti.index') }}" class="btn btn-secondary">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection