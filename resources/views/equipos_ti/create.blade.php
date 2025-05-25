@extends('layouts.app')

@section('title', 'Añadir Nuevo Equipo TI')

@section('content')
{{-- Incluimos creador.css si tiene estilos específicos para formularios --}}
<link href="{{ asset('css/creador.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Usamos la columna de Bootstrap --}}
            <div class="card">
                <div class="card-header">{{ __('Añadir Nuevo Equipo TI') }}</div>

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

                    <form action="{{ route('equipos-ti.store') }}" method="POST">
                        @csrf

                        <div class="mb-3"> {{-- Clase mb-3 para margen inferior --}}
                            <label for="nombre_equipo" class="form-label">{{ __('Nombre del Equipo') }}</label>
                            <input type="text" name="nombre_equipo" id="nombre_equipo"
                                   class="form-control @error('nombre_equipo') is-invalid @enderror"
                                   value="{{ old('nombre_equipo') }}" required autocomplete="off">
                            @error('nombre_equipo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">{{ __('Ubicación') }}</label>
                            <input type="text" name="ubicacion" id="ubicacion"
                                   class="form-control @error('ubicacion') is-invalid @enderror"
                                   value="{{ old('ubicacion') }}" required autocomplete="off">
                            @error('ubicacion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">{{ __('Estado') }}</label>
                            <select name="estado" id="estado"
                                    class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="">{{ __('Seleccione un estado') }}</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
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
                            <select name="usuario_asignado_id" id="usuario_asignado_id"
                                    class="form-select @error('usuario_asignado_id') is-invalid @enderror">
                                <option value="">{{ __('Ninguno') }}</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('usuario_asignado_id') == $usuario->id ? 'selected' : '' }}>
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
                            <input type="text" name="numero_serie" id="numero_serie"
                                   class="form-control @error('numero_serie') is-invalid @enderror"
                                   value="{{ old('numero_serie') }}" autocomplete="off">
                            @error('numero_serie')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">{{ __('Modelo') }} (Opcional)</label>
                            <input type="text" name="modelo" id="modelo"
                                   class="form-control @error('modelo') is-invalid @enderror"
                                   value="{{ old('modelo') }}" autocomplete="off">
                            @error('modelo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="marca" class="form-label">{{ __('Marca') }} (Opcional)</label>
                            <input type="text" name="marca" id="marca"
                                   class="form-control @error('marca') is-invalid @enderror"
                                   value="{{ old('marca') }}" autocomplete="off">
                            @error('marca')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_adquisicion" class="form-label">{{ __('Fecha de Adquisición') }} (Opcional)</label>
                            <input type="date" name="fecha_adquisicion" id="fecha_adquisicion"
                                   class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                                   value="{{ old('fecha_adquisicion') }}">
                            @error('fecha_adquisicion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">{{ __('Descripción') }} (Opcional)</label>
                            <textarea name="descripcion" id="descripcion" rows="4"
                                      class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2"> {{-- Clases para botones apilados con espacio --}}
                            <button type="submit" class="btn btn-primary">
                                {{ __('Guardar Equipo') }}
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