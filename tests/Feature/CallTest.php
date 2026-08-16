<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Manager;
use Tests\TestCase;

class CallTest extends TestCase
{
    private Manager $manager;

    private Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = Manager::factory()->create();
        $this->lead = Lead::factory()->create();
    }

    public function test_create_call_successfully(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'duration' => 120,
                'result' => 'callback_later',
                'manager_id' => $this->manager->id,
                'lead_id' => $this->lead->id,
            ]);
    }

    public function test_invalid_duration(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 'not_a_number',
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('duration');
    }

    public function test_negative_duration(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => -1,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('duration');
    }

    public function test_invalid_result(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 120,
            'result' => 'invalid_result',
            'manager_id' => $this->manager->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('result');
    }

    public function test_nonexistent_manager(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('manager_id');
    }

    public function test_nonexistent_lead(): void
    {
        $response = $this->postJson('/api/leads/9999/calls', [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $response->assertStatus(404);
    }

    public function test_call_persists_correctly(): void
    {
        $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertDatabaseHas('calls', [
            'lead_id' => $this->lead->id,
            'manager_id' => $this->manager->id,
            'duration' => 120,
            'result' => 'callback_later',
        ]);
    }

    public function test_manager_id_is_required(): void
    {
        $response = $this->postJson("/api/leads/{$this->lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('manager_id');
    }
}
