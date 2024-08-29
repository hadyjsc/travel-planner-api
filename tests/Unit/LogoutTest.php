<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    /**
     * Normal test if logout successfuly.
     * - headers bearer token from login
     */
    public function test_logout_is_successfuly(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data'
        ]);
    }

    /**
     * Negative test logout unsuccess
     * - No token used
     */
    public function test_logout_no_token() : void 
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }
}
