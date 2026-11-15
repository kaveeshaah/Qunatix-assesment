<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Employee,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => UserRole::Manager]);
    }

    public function operations(): static
    {
        return $this->state(fn () => ['role' => UserRole::Operations]);
    }

    public function reportingTo(User $manager): static
    {
        return $this->state(fn () => ['manager_id' => $manager->id]);
    }
}