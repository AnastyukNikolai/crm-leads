<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use App\Models\Manager;
use Tests\TestCase;

class ManagerLeadsTest extends TestCase
{
    public function test_returns_only_manager_leads(): void
    {
        $managerA = Manager::factory()->create();
        $managerB = Manager::factory()->create();

        $leadA = Lead::factory()->create(['manager_id' => $managerA->id]);
        $leadB = Lead::factory()->create(['manager_id' => $managerA->id]);
        Lead::factory()->create(['manager_id' => $managerB->id]);

        $response = $this->getJson("/api/managers/{$managerA->id}/leads");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertContains($leadA->id, $ids);
        $this->assertContains($leadB->id, $ids);
    }

    public function test_calls_count_is_correct(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->create(['manager_id' => $manager->id]);

        Call::factory()->count(3)->create([
            'lead_id' => $lead->id,
            'manager_id' => $manager->id,
        ]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads");
        $leadData = $response->json('data.0');

        $this->assertEquals(3, $leadData['calls_count']);
    }

    public function test_total_call_duration_is_correct(): void
    {
        $manager = Manager::factory()->create();
        $lead = Lead::factory()->create(['manager_id' => $manager->id]);

        Call::factory()->create([
            'lead_id' => $lead->id,
            'manager_id' => $manager->id,
            'duration' => 100,
        ]);
        Call::factory()->create([
            'lead_id' => $lead->id,
            'manager_id' => $manager->id,
            'duration' => 200,
        ]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads");
        $leadData = $response->json('data.0');

        $this->assertEquals(300, $leadData['total_call_duration']);
    }

    public function test_status_is_correct(): void
    {
        $manager = Manager::factory()->create();
        Lead::factory()->create([
            'manager_id' => $manager->id,
            'status' => LeadStatus::InProgress,
        ]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads");

        $this->assertEquals('in_progress', $response->json('data.0.status'));
    }

    public function test_response_structure(): void
    {
        $manager = Manager::factory()->create();
        Lead::factory()->create(['manager_id' => $manager->id]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads");
        $leadData = $response->json('data.0');

        $this->assertArrayHasKey('id', $leadData);
        $this->assertArrayHasKey('name', $leadData);
        $this->assertArrayHasKey('status', $leadData);
        $this->assertArrayHasKey('calls_count', $leadData);
        $this->assertArrayHasKey('total_call_duration', $leadData);
    }

    public function test_empty_calls_shows_zero_values(): void
    {
        $manager = Manager::factory()->create();
        Lead::factory()->create(['manager_id' => $manager->id]);

        $response = $this->getJson("/api/managers/{$manager->id}/leads");
        $leadData = $response->json('data.0');

        $this->assertEquals(0, $leadData['calls_count']);
        $this->assertEquals(0, $leadData['total_call_duration']);
    }
}
