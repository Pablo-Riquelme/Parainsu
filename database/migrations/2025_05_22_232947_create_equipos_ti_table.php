<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipos_ti', function (Blueprint $table) {
            $table->id(); // Columna para el ID del equipo (auto-incrementing, primary key)
            $table->string('nombre_equipo')->unique(); // Nombre del equipo (ej. "Desktop-001", "Laptop-RH2023")
            $table->string('ubicacion'); // Ubicación del equipo (ej. "Oficina 302", "Sala de Reuniones", "Almacén TI")
            $table->enum('estado', ['en_uso', 'en_desuso', 'en_reparacion', 'disponible']); // Estado del equipo
            $table->text('descripcion')->nullable(); // Descripción opcional del equipo (especificaciones, etc.)
            $table->string('numero_serie')->unique()->nullable(); // Número de serie (opcional, pero útil)
            $table->string('modelo')->nullable(); // Modelo del equipo
            $table->string('marca')->nullable(); // Marca del equipo
            $table->date('fecha_adquisicion')->nullable(); // Fecha de adquisición
            $table->unsignedBigInteger('usuario_asignado_id')->nullable(); // ID del usuario al que está asignado
            $table->foreign('usuario_asignado_id')->references('id')->on('users')->onDelete('set null'); // Clave foránea a la tabla de usuarios

            $table->timestamps(); // Columnas `created_at` y `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos_ti');
    }
};