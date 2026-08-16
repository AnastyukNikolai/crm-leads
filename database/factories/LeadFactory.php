<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'status' => LeadStatus::New,
            'manager_id' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::InProgress,
        ]);
    }

    public function won(): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::Won,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::Lost,
        ]);
    }

    public function withManager(Manager|int|null $manager = null): static
    {
        return $this->state(fn () => [
            'manager_id' => $manager instanceof Manager ? $manager->id : $manager ?? ManagerFactory::new(),
        ]);
    }
}
