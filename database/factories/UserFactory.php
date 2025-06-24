<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => 1, // 
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Define the user as an admin_ti.
     * Asigna el role_id correspondiente al rol 'admin_ti'.
     */
    public function adminTi(): static
    {
        return $this->state(fn (array $attributes) => [
            // NECESITARÁS SABER CUÁL ES EL ID REAL DEL ROL 'admin_ti' en tu tabla de roles
            // Por ejemplo, si 'admin_ti' es el ID 1
            'role_id' => 1, // <--- AJUSTA ESTE ID al ID real de tu rol 'admin_ti'
        ]);
    }

    /**
     * Define the user as bodega.
     * Asigna el role_id correspondiente al rol 'bodega'.
     */
    public function bodega(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 2, // <--- AJUSTA ESTE ID al ID real de tu rol 'bodega'
        ]);
    }
}
