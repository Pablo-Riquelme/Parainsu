<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB; // Importar DB para inserciones directas

    class RolesTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            DB::table('roles')->insert([ // Asegúrate de que 'roles' sea el nombre correcto de tu tabla de roles
                [
                    'id' => 1, // Asegúrate de que este ID sea el que usas para 'admin_ti' en tu factory
                    'name' => 'admin_ti',
                    // 'description' => 'Administrador de TI',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2, // Asegúrate de que este ID sea el que usas para 'bodega' en tu factory
                    'name' => 'bodega',
                    // 'description' => 'Usuario de Bodega',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3, // Asegúrate de que este ID sea el que usas para 'user' en tu factory
                    'name' => 'user',
                    // 'description' => 'Usuario regular',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Añade aquí cualquier otro rol que necesites para tus pruebas
            ]);
        }
    }
    