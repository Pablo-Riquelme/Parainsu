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
            // Añade la columna 'id_afectada' después de 'tabla_afectada'.
            // unsignedBigInteger es adecuado para IDs (enteros positivos grandes).
            // nullable() permite que la columna pueda estar vacía si no todos los tipos de movimientos la usan.
            // Considerando tu código actual, siempre se llenará para equipos y, si en el futuro añades insumos,
            // podría ser útil que sea nullable si no ambos campos (equipo_ti_id e insumo_medico_id)
            // siempre se llenarán junto con id_afectada.
            $table->unsignedBigInteger('id_afectada')->nullable()->after('tabla_afectada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Define cómo deshacer el cambio: elimina la columna.
            $table->dropColumn('id_afectada');
        });
    }
};