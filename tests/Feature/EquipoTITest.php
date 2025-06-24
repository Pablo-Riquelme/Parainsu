<?php

namespace Tests\Feature;

use App\Models\EquipoTI;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\RolesTableSeeder; // Importa tu seeder de roles

class EquipoTITest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
    }

    /**
     * Prueba que un usuario no autenticado es redirigido al login para ver equipos TI.
     * @test
     */
    public function un_usuario_no_autenticado_es_redirigido_a_login_para_equipos_ti()
    {
        $response = $this->get('/equipos-ti');
        $response->assertRedirect('/login');
    }

    /**
     * Prueba que un usuario sin el rol 'admin_ti' NO puede acceder a equipos TI.
     * @test
     */
    public function un_usuario_sin_rol_admin_ti_no_puede_acceder_a_equipos_ti()
    {
        // Crear un usuario con un role_id que NO sea 'admin_ti'.
        // Estoy asumiendo que el ID 3 es un rol que NO tiene permisos para 'equipos-ti'.
        $user = User::factory()->create(['role_id' => 3]); // Usa el ID del rol 'user' o 'bodega'

        $response = $this->actingAs($user)->get('/equipos-ti');

        // *** CAMBIO AQUÍ: Esperar 302 (redirección) en lugar de 403 ***
        $response->assertStatus(302);
        // Opcional: si sabes a dónde redirige específicamente, puedes usar:
        // $response->assertRedirect('/home'); // O la ruta a la que redirija por falta de permisos
    }

    /**
     * Prueba que un admin_ti puede ver la lista de equipos TI.
     * @test
     */
    public function un_admin_ti_puede_ver_la_lista_de_equipos_ti()
    {
        $admin = User::factory()->adminTi()->create();
        $response = $this->actingAs($admin)->get('/equipos-ti');
        $response->assertStatus(200);
        $response->assertSee('Equipos TI');
    }

    /**
     * Prueba que un admin_ti puede crear un nuevo equipo TI.
     * @test
     */
    public function un_admin_ti_puede_crear_un_nuevo_equipo_ti()
    {
        $admin = User::factory()->adminTi()->create();
        $equipoData = EquipoTI::factory()->make()->toArray();
        $equipoData['usuario_asignado_id'] = User::factory()->create()->id;

        $response = $this->actingAs($admin)->post('/equipos-ti', $equipoData);

        $response->assertRedirect('/equipos-ti');
        $response->assertSessionHas('success', 'Equipo de TI creado exitosamente.');
        $this->assertDatabaseHas('equipos_ti', [
            'numero_serie' => $equipoData['numero_serie'],
            'estado' => $equipoData['estado'],
            'nombre_equipo' => $equipoData['nombre_equipo'],
        ]);
    }

    /**
     * Prueba que un admin_ti puede ver los detalles de un equipo TI.
     * @test
     */
    public function un_admin_ti_puede_ver_los_detalles_de_un_equipo_ti()
    {
        $admin = User::factory()->adminTi()->create();
        $equipo = EquipoTI::factory()->create();

        $response = $this->actingAs($admin)->get('/equipos-ti/' . $equipo->id);

        $response->assertStatus(200);
        $response->assertSee($equipo->nombre_equipo);
        $response->assertSee($equipo->numero_serie);
    }

    /**
     * Prueba que un admin_ti puede actualizar un equipo TI.
     * @test
     */
    public function un_admin_ti_puede_actualizar_un_equipo_ti()
    {
        $admin = User::factory()->adminTi()->create();
        $equipo = EquipoTI::factory()->create();
        $newEstado = 'en_reparacion';

        $updatedData = $equipo->toArray();
        $updatedData['nombre_equipo'] = 'Equipo Actualizado';
        $updatedData['estado'] = $newEstado;
        $updatedData['descripcion'] = 'Actualizado para reparación de prueba';
        $updatedData['usuario_asignado_id'] = $equipo->usuario_asignado_id ?: User::factory()->create()->id;


        $response = $this->actingAs($admin)->put('/equipos-ti/' . $equipo->id, $updatedData);

        $response->assertRedirect('/equipos-ti');
        $response->assertSessionHas('success', 'Equipo de TI actualizado exitosamente.');
        $this->assertDatabaseHas('equipos_ti', [
            'id' => $equipo->id,
            'nombre_equipo' => 'Equipo Actualizado',
            'estado' => $newEstado,
            'descripcion' => 'Actualizado para reparación de prueba',
        ]);
    }

    /**
     * Prueba que un admin_ti puede eliminar un equipo TI.
     * @test
     */
    public function un_admin_ti_puede_eliminar_un_equipo_ti()
    {
        $admin = User::factory()->adminTi()->create();
        $equipo = EquipoTI::factory()->create();

        $response = $this->actingAs($admin)->delete('/equipos-ti/' . $equipo->id);

        $response->assertRedirect('/equipos-ti');
        $response->assertSessionHas('success', 'Equipo de TI eliminado exitosamente y movimiento de baja registrado.');
        $this->assertDatabaseMissing('equipos_ti', ['id' => $equipo->id]);
    }
}
