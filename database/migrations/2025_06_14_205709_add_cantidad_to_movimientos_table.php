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
            // Añade la columna 'cantidad' de tipo entero, no puede ser nula.
            // Si necesitas que sea opcional, añade ->nullable()
            $table->integer('cantidad')->after('tipo'); // La colocamos después de 'tipo' para un buen orden
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }
};