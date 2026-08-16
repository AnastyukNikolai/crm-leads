<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use Tests\TestCase;

class LeadTest extends TestCase
{
    public function test_create_lead_successfully(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'John Doe',
            'phone' => '+380991234567',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'phone' => '+380991234567',
                'status' => 'new',
                'manager_id' => null,
            ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'John Doe',
            'status' => 'new',
            'manager_id' => null,
        ]);
    }

    public function test_status_is_automatically_new(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'Test Lead',
            'phone' => '+380991234567',
        ]);

        $response->assertJsonPath('data.status', LeadStatus::New->value);
    }

    public function test_manager_id_is_automatically_null(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'Test Lead',
            'phone' => '+380991234567',
        ]);

        $response->assertJsonPath('data.manager_id', null);
    }

    public function test_name_is_required(): void
    {
        $response = $this->postJson('/api/leads', [
            'phone' => '+380991234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_phone_is_required(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'John Doe',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('phone');
    }

    public function test_status_cannot_be_set_via_api(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'John Doe',
            'phone' => '+380991234567',
            'status' => 'won',
        ]);

        $response->assertJsonPath('data.status', LeadStatus::New->value);
    }

    public function test_manager_id_cannot_be_set_via_api(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'John Doe',
            'phone' => '+380991234567',
            'manager_id' => 5,
        ]);

        $response->assertJsonPath('data.manager_id', null);
    }
}
