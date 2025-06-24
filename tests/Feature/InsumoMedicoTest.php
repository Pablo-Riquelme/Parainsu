<?php

namespace Tests\Feature;

use App\Models\InsumoMedico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\RolesTableSeeder; // Asegúrate de que este Seeder esté configurado correctamente

class InsumoMedicoTest extends TestCase
{
    use RefreshDatabase; // Resetea la base de datos para cada prueba
    use WithFaker;     // Para generar datos falsos

    /**
     * Se ejecuta antes de cada método de prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Asegúrate de que tus factories UserFactory tienen los estados adminTi() y bodega()
        $this->seed(RolesTableSeeder::class);
    }

    /**
     * Prueba que un usuario no autenticado es redirigido al login para ver insumos.
     * @test
     */
    public function un_usuario_no_autenticado_es_redirigido_a_login_para_insumos()
    {
        $response = $this->get('/insumos-medicos');
        $response->assertRedirect('/login');
    }

    /**
     * Prueba que un usuario con rol 'admin_ti' puede ver la lista de insumos.
     * @test
     */
    public function un_admin_ti_puede_ver_la_lista_de_insumos_medicos()
    {
        $admin = User::factory()->adminTi()->create();
        $response = $this->actingAs($admin)->get('/insumos-medicos');
        $response->assertStatus(200);
        $response->assertSee('Insumos Médicos'); // O el texto que identifique la lista de insumos
    }

    /**
     * Prueba que un usuario con rol 'bodega' puede ver la lista de insumos.
     * @test
     */
    public function un_bodega_puede_ver_la_lista_de_insumos_medicos()
    {
        $bodega = User::factory()->bodega()->create();
        $response = $this->actingAs($bodega)->get('/insumos-medicos');
        $response->assertStatus(200);
        $response->assertSee('Insumos Médicos'); // O el texto que identifique la lista de insumos
    }

    /**
     * Prueba que un admin_ti puede crear un nuevo insumo médico.
     * @test
     */
    public function un_admin_ti_puede_crear_un_nuevo_insumo_medico()
    {
        $admin = User::factory()->adminTi()->create();
        $insumoData = InsumoMedico::factory()->make()->toArray(); // Genera datos sin guardarlos aún

        $response = $this->actingAs($admin)->post('/insumos-medicos', $insumoData);

        $response->assertRedirect('/insumos-medicos');
        $response->assertSessionHas('success', 'Insumo médico creado exitosamente.'); // Verifica el mensaje de éxito
        $this->assertDatabaseHas('insumos_medicos', [
            'nombre' => $insumoData['nombre'], // Usar 'nombre' según tu tabla
            'stock' => $insumoData['stock'],   // Usar 'stock' según tu tabla
        ]);
    }

    /**
     * Prueba que un admin_ti puede ver los detalles de un insumo.
     * @test
     */
    public function un_admin_ti_puede_ver_los_detalles_de_un_insumo_medico()
    {
        $admin = User::factory()->adminTi()->create();
        $insumo = InsumoMedico::factory()->create();

        $response = $this->actingAs($admin)->get('/insumos-medicos/' . $insumo->id);

        $response->assertStatus(200);
        $response->assertSee($insumo->nombre); // Usar 'nombre' según tu tabla
    }

    /**
     * Prueba que un admin_ti puede actualizar un insumo médico.
     * @test
     */
    public function un_admin_ti_puede_actualizar_un_insumo_medico()
    {
        $admin = User::factory()->adminTi()->create();
        $insumo = InsumoMedico::factory()->create();
        $newStock = $insumo->stock + 50;

        $updatedData = $insumo->toArray();
        $updatedData['nombre'] = 'Nombre Actualizado de Insumo'; // Usar 'nombre'
        $updatedData['stock'] = $newStock;                       // Usar 'stock'

        $response = $this->actingAs($admin)->put('/insumos-medicos/' . $insumo->id, $updatedData);

        $response->assertRedirect('/insumos-medicos');
        $response->assertSessionHas('success', 'Insumo médico actualizado exitosamente.');
        $this->assertDatabaseHas('insumos_medicos', [
            'id' => $insumo->id,
            'nombre' => 'Nombre Actualizado de Insumo',
            'stock' => $newStock,
        ]);
        // Si el stock se actualiza y esto genera un movimiento, puedes añadir una aserción aquí:
        // $this->assertDatabaseHas('movimientos', [
        //     'insumo_id' => $insumo->id,
        //     'tipo_movimiento' => 'entrada',
        //     'cantidad' => 50,
        // ]);
    }

    /**
     * Prueba que un admin_ti puede eliminar un insumo médico.
     * @test
     */
    public function un_admin_ti_puede_eliminar_un_insumo_medico()
    {
        $admin = User::factory()->adminTi()->create();
        $insumo = InsumoMedico::factory()->create();

        $response = $this->actingAs($admin)->delete('/insumos-medicos/' . $insumo->id);

        $response->assertRedirect('/insumos-medicos');
        $response->assertSessionHas('success', 'Insumo médico eliminado exitosamente.');
        $this->assertDatabaseMissing('insumos_medicos', ['id' => $insumo->id]);
    }

    /**
     * Prueba que la exportación a Excel funciona para admin_ti.
     * @test
     */
    public function un_admin_ti_puede_exportar_insumos_a_excel()
    {
        $admin = User::factory()->adminTi()->create();
        InsumoMedico::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/insumos-medicos/export/excel');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertDownload('insumos_medicos.xlsx');
    }

    /**
     * Prueba que la exportación a PDF funciona para admin_ti.
     * @test
     */
    public function un_admin_ti_puede_exportar_insumos_a_pdf()
    {
        $admin = User::factory()->adminTi()->create();
        InsumoMedico::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/insumos-medicos/export/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertDownload('insumos_medicos.pdf');
    }
}
