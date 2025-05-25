<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin_ti']);
        Role::create(['name' => 'bodega']);
        Role::create(['name' => 'usuario_normal']);
    }
}