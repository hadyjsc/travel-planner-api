<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Normal test if login successfuly.
     */
    public function test_login_is_successfuly(): void
    {
        $email = 'kelepon@kueenak.com';
        $pass = 'kuekelep0n';

         $user = User::factory()->create([
            'name' => 'Kelepon Merah',
            'email' => $email,
            'password' => Hash::make($pass),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $pass,
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'success', 
            'data' => ['access_token', 'token_type'], 
            'messageTitle', 
            'message'
        ]);
    }

    /**
     * Negative test if field username is required.
     * - if field of username is empty but another field is filled
     */
    public function test_login_validation_username_required() : void 
    {
        $response = $this->postJson('/api/login', [
            'password' => 'kuekelep0n',
        ]);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'email' ]
        ]);

        $response->assertSee('The email field is required.');
    }

    /**
     * Negative test if field password is required.
     * - if field of password is empty but another field is filled
     */
    public function test_login_validation_password_required() : void 
    {
        
    }

    /**
     * Negative test if field password is required.
     * - Invalid username and password
     */
    public function test_login_invalid_username_and_password() : void 
    {
        
    }

    /**
     * Negative test if field username is fill in with invalid email
     * - Fill in form username with invalid email format
     */
    public function test_login_username_with_invalid_email() : void
    {

    }
}
