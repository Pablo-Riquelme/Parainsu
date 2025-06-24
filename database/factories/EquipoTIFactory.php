<?php

    namespace Database\Factories;

    use App\Models\EquipoTI;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipoTI>
     */
    class EquipoTIFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = EquipoTI::class;

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {
            return [
                'nombre_equipo' => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['Laptop', 'Monitor', 'Impresora']),
                'ubicacion' => $this->faker->address(),
                // ¡IMPORTANTE! Asegúrate de que estos valores coincidan EXACTAMENTE con tu ENUM de la base de datos
                'estado' => $this->faker->randomElement(['en_uso', 'en_desuso', 'en_reparacion', 'disponible']),
                'descripcion' => $this->faker->optional()->paragraph(1),
                'numero_serie' => $this->faker->unique()->regexify('[A-Z0-9]{10,20}'),
                'modelo' => $this->faker->word() . ' ' . $this->faker->numberBetween(1000, 9999),
                'marca' => $this->faker->company(),
                'fecha_adquisicion' => $this->faker->date('Y-m-d', 'now'),
                // 'garantia_hasta' no está en tu tabla 'equipos_ti', la quito.
                // 'observaciones' está en tu tabla como 'descripcion', que ya la tenemos.
                'usuario_asignado_id' => User::factory()->create()->id, // Asigna un usuario existente
            ];
        }
    }
    