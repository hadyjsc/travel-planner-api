<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Normal test if register is successfuly
     * any fields according to the form criteria should be return success
     * - field name
     * - field email
     * - field password
     * - field password_confirmation
     */
    public function test_register_is_successfully(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Unit',
            'email' => 'unit@timhore.com',
            'password' => 'berhasilhore3',
            'password_confirmation' => 'berhasilhore3',
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'success', 
            'data' => ['access_token', 'token_type'], 
            'messageTitle', 
            'message'
        ]);
    }

    /**
     * Negative test if field name is required
     * - if field of name is empty but another field is filled
     */
    function test_register_validation_name_required() : void 
    {
        $response = $this->postJson('/api/register', [
            'email' => 'unit@timhore.com',
            'password' => 'berhasilhore3',
            'password_confirmation' => 'berhasilhore3',
        ]);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'name' ]
        ]);

        $response->assertSee('The name field is required.');
    }

    /**
     * Negative test if field email is required
     * - if field of email is empty but another field is filled
     */
    function test_register_validation_email_required() : void 
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Unit',
            'password' => 'berhasilhore3',
            'password_confirmation' => 'berhasilhore3',
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
     * Negative test if field password is required
     * - if field of password is empty but another field is filled
     */
    function test_register_validation_password_required() : void 
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Unit',
            'email' => 'unit@timhore.com',
            'password_confirmation' => 'berhasilhore3',
        ]);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'password' ]
        ]);

        $response->assertSee('The password field is required.');
    }

    /**
     * Negative test if field password confirmation is required
     * - if field of password confirmation is empty but another field is filled
     */
    function test_register_validation_password_confirmation_required() : void 
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Unit',
            'email' => 'unit@timhore.com',
            'password' => 'berhasilhore3',
        ]);
        
        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'password' ]
        ]);

        $response->assertSee('The password field confirmation does not match.');
    }

    /**
     * Negative test if field all field is required
     * - if all fields is empty
     */
    function test_register_validation_all_field_required() : void 
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'name', 'email', 'password']
        ]);

        $response->assertSee('The name field is required.');
        $response->assertSee('The email field is required.');
        $response->assertSee('The password field is required.');
    }

    /**
     * Negative test if password fill in min 8 char
     */
    function test_register_validation_password_min_character() : void 
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Unit',
            'email' => 'unit@timhore.com',
            'password' => 'h0r3',
            'password_confirmation' => 'h0r3',
        ]);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'password' ]
        ]);

        $response->assertSee('The password field must be at least 8 characters.');
    }
}
