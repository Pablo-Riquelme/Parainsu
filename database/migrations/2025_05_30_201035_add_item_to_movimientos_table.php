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
        Schema::table('movimientos', function (Blueprint $table) {
            // Añadir insumo_medico_id como clave foránea nullable
            $table->foreignId('insumo_medico_id')->nullable()->constrained('insumos_medicos')->onDelete('set null');

            // Añadir equipo_ti_id como clave foránea nullable
            $table->foreignId('equipo_ti_id')->nullable()->constrained('equipos_ti')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Eliminar las claves foráneas primero
            $table->dropConstrainedForeignId('insumo_medico_id');
            $table->dropConstrainedForeignId('equipo_ti_id');

            // Eliminar las columnas
            // Si Laravel es versión < 8.x:
            // $table->dropColumn(['insumo_medico_id', 'equipo_ti_id']);
            // Si Laravel es versión >= 8.x (mejor práctica):
            $table->dropColumn('insumo_medico_id');
            $table->dropColumn('equipo_ti_id');
        });
    }
};