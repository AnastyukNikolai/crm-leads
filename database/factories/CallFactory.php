<?php

namespace Database\Factories;

use App\Enums\CallResult;
use App\Models\Call;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        return [
            'lead_id' => LeadFactory::new(),
            'manager_id' => ManagerFactory::new(),
            'duration' => fake()->numberBetween(10, 600),
            'result' => fake()->randomElement(CallResult::cases()),
        ];
    }

    public function noAnswer(): static
    {
        return $this->state(fn () => [
            'result' => CallResult::NoAnswer,
        ]);
    }

    public function callbackLater(): static
    {
        return $this->state(fn () => [
            'result' => CallResult::CallbackLater,
        ]);
    }

    public function success(): static
    {
        return $this->state(fn () => [
            'result' => CallResult::Success,
        ]);
    }
}
