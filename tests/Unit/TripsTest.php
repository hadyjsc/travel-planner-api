<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Trip;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripsTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * Full normal test for create trip planner
     * - test with valid user login
     * - create data plan
     */
    public function test_trip_create(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Jalan ke kota tua',
            'origin' => 'Rengat',
            'destination' => 'Jakarta',
            'schedule_start_date' => '2024-12-01',
            'schedule_end_date' => '2024-12-05',
            'type' => 'multi-day',
            'description' => 'Pokoknya jalan-jalan saja, karena sudah lama tidak jalan-jalan.',
        ];

        $response = $this->postJson('/api/trips', $payload);
        
        $response->assertStatus(201)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => ['title', 'created_by', 'created_at']
        ]);
    }

    /**
     * Negative test with some validation to show the valdation is working well in create function
     */
    public function test_trip_create_validation_title(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => '',
            'origin' => 'Rengat',
            'destination' => 'Bandung',
            'schedule_start_date' => '2024-12-01',
            'schedule_end_date' => '2024-12-05',
            'type' => 'multi-day',
            'description' => 'Pokoknya jalan-jalan saja, karena sudah lama tidak jalan-jalan.',
        ];
        $response = $this->postJson('/api/trips', $payload);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'title' ]
        ]);
    }

    /**
     * Full normal test for update a trip planner data
     * - test with valid user login
     * - update data plan
     */
    public function test_trip_update() : void 
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $trip = Trip::factory()->create(['title' => 'Jalan Saja ke mana', 'type' => 2, 'created_by' => $user->id]);
        
        $payload = [
            'title' => 'Jalan Ke Kota Tua',
            'origin' => 'Rengat',
            'destination' => 'Jakarta',
            'schedule_start_date' => '2024-12-01',
            'schedule_end_date' => '2024-12-07',
            'type' => 'multi-day',
            'description' => 'Pokoknya jalan-jalan saja, karena sudah lama tidak jalan-jalan.',
        ];

        $response = $this->putJson("/api/trips/{$trip->id}", $payload);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => ['title']
        ]);
    }

    /**
     * Negative test with some validation to show the valdation is working well in update function
     */
    public function test_trip_update_validation_title() : void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $trip = Trip::factory()->create(['created_by' => $user->id]);

        $payload = [
            'title' => '',
            'origin' => '',
            'destination' => 'Bandung',
            'schedule_start_date' => '2024-12-01',
            'schedule_end_date' => '2024-12-05',
            'type' => 'multi-day',
            'description' => 'Pokoknya jalan-jalan saja, karena sudah lama tidak jalan-jalan.',
        ];

        $response = $this->putJson("/api/trips/{$trip->id}", $payload);

        $response->assertStatus(400)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data' => [ 'title', 'origin' ]
        ]);
    }

    /**
     * Full normal test for delete a trip planner data
     * - test with valid user login
     * - delete data plan
     */
    public function test_trip_delete() : void 
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $trip = Trip::factory()->create(['created_by' => $user->id]);

        $response = $this->deleteJson("/api/trips/{$trip->id}");

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'messageTitle',
            'message',
            'data'
        ]);
    }

    /**
     * Negative test with some delete data with invalid param id
     */
    public function test_trip_delete_invalid_data() : void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/trips/666");

        $response->assertStatus(500); 
        $response->assertSee('No query results for model [App\\\Models\\\Trip].');
    }

    /**
     * Full normal test for get a trip planner data
     * - test with valid user login
     * - get data based on created_by = loggedin user
     * - get data if is_deleted = false
     */
    public function test_trip_retrive() : void {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $trip = Trip::factory()->create([
            'created_by' => $user->id,
            'is_deleted' => false,
        ]);

        $response = $this->getJson('/api/trips');
        
        $response->assertStatus(200);
    }

    /**
     * Negative test unauthorized user access the api
     */
    public function test_trip_retrive_with_unauthorized_user() : void {
        $response = $this->getJson('/api/trips');
        
        $response->assertStatus(401);
    }
}
