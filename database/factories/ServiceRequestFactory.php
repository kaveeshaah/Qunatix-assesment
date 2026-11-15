<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ServiceRequest> */
class ServiceRequestFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['it_support', 'equipment', 'maintenance', 'access']),
            'status' => RequestStatus::Draft,
        ];
    }

    public function withStatus(RequestStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}