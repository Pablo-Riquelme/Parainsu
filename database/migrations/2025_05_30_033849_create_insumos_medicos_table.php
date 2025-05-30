<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsumosMedicosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insumos_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('unidad_medida'); // Ej. 'ml', 'gramos', 'unidad'
            $table->integer('stock');
            $table->integer('stock_minimo')->default(5); // Umbral para alertas
            $table->decimal('precio', 10, 2)->nullable();
            $table->string('proveedor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_medicos');
    }
}