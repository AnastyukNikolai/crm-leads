<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Call;
use App\Models\Lead;
use App\Models\Manager;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    private Manager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = Manager::factory()->create();
    }

    public function test_first_call_changes_new_to_in_progress(): void
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::New]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::InProgress->value, $lead->fresh()->status->value);
    }

    public function test_automatic_manager_assignment_when_lead_has_no_manager(): void
    {
        $lead = Lead::factory()->create(['manager_id' => null]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals($this->manager->id, $lead->fresh()->manager_id);
    }

    public function test_manager_unchanged_when_lead_already_has_manager(): void
    {
        $existingManager = Manager::factory()->create();
        $lead = Lead::factory()->create(['manager_id' => $existingManager->id]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals($existingManager->id, $lead->fresh()->manager_id);
    }

    public function test_success_result_changes_status_to_won(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::InProgress,
            'manager_id' => $this->manager->id,
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'success',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Won->value, $lead->fresh()->status->value);
    }

    public function test_success_from_new_changes_to_won_not_in_progress(): void
    {
        $lead = Lead::factory()->create(['status' => LeadStatus::New]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'success',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Won->value, $lead->fresh()->status->value);
    }

    public function test_three_consecutive_no_answer_changes_to_lost(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::InProgress,
            'manager_id' => $this->manager->id,
        ]);

        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(30),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(20),
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 10,
            'result' => 'no_answer',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Lost->value, $lead->fresh()->status->value);
    }

    public function test_non_consecutive_no_answer_does_not_trigger_lost(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::InProgress,
            'manager_id' => $this->manager->id,
        ]);

        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(30),
        ]);
        Call::factory()->callbackLater()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(20),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(10),
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 10,
            'result' => 'no_answer',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::InProgress->value, $lead->fresh()->status->value);
    }

    public function test_old_no_answer_calls_are_not_considered(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::InProgress,
            'manager_id' => $this->manager->id,
        ]);

        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subDays(10),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subDays(9),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subDays(8),
        ]);
        Call::factory()->callbackLater()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(30),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(20),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(10),
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 10,
            'result' => 'no_answer',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Lost->value, $lead->fresh()->status->value);
    }

    public function test_terminal_won_does_not_revert_to_in_progress(): void
    {
        $lead = Lead::factory()->won()->create(['manager_id' => $this->manager->id]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Won->value, $lead->fresh()->status->value);
    }

    public function test_terminal_lost_does_not_revert_to_in_progress(): void
    {
        $lead = Lead::factory()->lost()->create(['manager_id' => $this->manager->id]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'callback_later',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Lost->value, $lead->fresh()->status->value);
    }

    public function test_success_overrides_no_answer_pattern(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::InProgress,
            'manager_id' => $this->manager->id,
        ]);

        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(30),
        ]);
        Call::factory()->noAnswer()->create([
            'lead_id' => $lead->id,
            'manager_id' => $this->manager->id,
            'created_at' => now()->subMinutes(20),
        ]);

        $this->postJson("/api/leads/{$lead->id}/calls", [
            'duration' => 120,
            'result' => 'success',
            'manager_id' => $this->manager->id,
        ]);

        $this->assertEquals(LeadStatus::Won->value, $lead->fresh()->status->value);
    }
}
