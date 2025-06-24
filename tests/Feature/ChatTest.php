<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\RolesTableSeeder;

class ChatTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesTableSeeder::class);
    }

    /**
     * Prueba que un usuario autenticado puede crear un chat privado con otro usuario.
     * @test
     */
    public function un_usuario_autenticado_puede_crear_un_chat_privado_con_otro_usuario()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson('/api/chats/private', [
            'recipient_user_id' => $user2->id,
        ]);

        // CAMBIAR assertStatus(200) A assertStatus(201)
        $response->assertStatus(201) // <--- Cambiado a 201 Created
                 ->assertJsonStructure(['message', 'chat_id']);

        $chatId = $response->json('chat_id');
        $this->assertDatabaseHas('chats', ['id' => $chatId]);

        $this->assertDatabaseHas('chat_user', [
            'chat_id' => $chatId,
            'user_id' => $user1->id,
        ]);
        $this->assertDatabaseHas('chat_user', [
            'chat_id' => $chatId,
            'user_id' => $user2->id,
        ]);

        $chat = Chat::find($chatId);
        $this->assertTrue($chat->users->contains($user1));
        $this->assertTrue($chat->users->contains($user2));
    }

    /**
     * Prueba que un usuario no autenticado no puede crear un chat privado (protección de API).
     * @test
     */
    public function un_usuario_no_autenticado_no_puede_crear_un_chat_privado()
    {
        $user2 = User::factory()->create();

        $response = $this->postJson('/api/chats/private', [
            'recipient_user_id' => $user2->id,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Prueba que no se puede crear un chat privado sin un ID de destinatario válido.
     * @test
     */
    public function no_se_puede_crear_chat_sin_id_destinatario_valido()
    {
        $user1 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson('/api/chats/private', [
            'recipient_user_id' => null,
        ]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('recipient_user_id');

        $response = $this->actingAs($user1)->postJson('/api/chats/private', [
            'recipient_user_id' => 999999,
        ]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('recipient_user_id');
    }

    /**
     * Prueba que un usuario no puede crear un chat consigo mismo.
     * (Asumiendo que tu ChatController tiene una validación para esto)
     * @test
     */
    public function un_usuario_no_puede_crear_un_chat_consigo_mismo()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/chats/private', [
            'recipient_user_id' => $user->id,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('recipient_user_id');
    }
}
