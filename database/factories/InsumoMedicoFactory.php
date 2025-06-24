<?php

namespace Database\Factories;

use App\Models\InsumoMedico; // Asegúrate de que esta línea esté presente
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsumoMedico>
 */
class InsumoMedicoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InsumoMedico::class; // Asegúrate de que el modelo esté asignado correctamente

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Los nombres de los campos deben coincidir EXACTAMENTE con las columnas de tu tabla 'insumos_medicos'
            'nombre' => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['Pastillas', 'Jeringas', 'Vendas', 'Alcohol']),
            'descripcion' => $this->faker->sentence(),
            'unidad_medida' => $this->faker->randomElement(['Unidades', 'Litros', 'Gramos', 'Cajas']),
            'stock' => $this->faker->numberBetween(10, 500),
            'stock_minimo' => $this->faker->numberBetween(1, 10), // Un valor razonable, según tu tabla es '5' por defecto
            'precio' => $this->faker->randomFloat(2, 1, 1000),
            'proveedor' => $this->faker->company(),
            // 'created_at' y 'updated_at' son manejados automáticamente por Laravel si los tienes como timestamps() en tu migración
        ];
    }
}
