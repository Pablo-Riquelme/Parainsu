<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // Ej. 'login', 'logout', 'creacion', 'modificacion', 'eliminacion'
            $table->string('tabla_afectada')->nullable(); // Opcional, para indicar qué tabla se modificó
            $table->integer('id_afectado')->nullable(); // Opcional, el ID del registro afectado
            $table->foreignId('user_id')->constrained()->nullable(); // Quién hizo el movimiento
            $table->text('descripcion')->nullable(); // Detalles del movimiento
            $table->json('datos_antes')->nullable(); // Datos antes del cambio (útil para updates)
            $table->json('datos_despues')->nullable(); // Datos después del cambio (útil para updates y creates)
             $table->ipAddress('ip_address')->nullable(); // Opcional: dirección IP del usuario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
}