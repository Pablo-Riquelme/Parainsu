<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed de roles
        $this->call(RoleSeeder::class);

        // Crear usuarios de prueba
        $adminTiRole = Role::where('name', 'admin_ti')->first();
        $bodegaRole = Role::where('name', 'bodega')->first();
        $usuarioNormalRole = Role::where('name', 'usuario_normal')->first();

        User::create([
            'name' => 'Admin TI',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminTiRole->id,
        ]);

        User::create([
            'name' => 'Usuario Bodega',
            'email' => 'bodega@example.com',
            'password' => Hash::make('password'),
            'role_id' => $bodegaRole->id,
        ]);

        User::create([
            'name' => 'Usuario Normal',
            'email' => 'normal@example.com',
            'password' => Hash::make('password'),
            'role_id' => $usuarioNormalRole->id,
        ]);
    }
}