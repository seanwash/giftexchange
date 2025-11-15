<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'name' => fake()->name(),
            'access_token' => \Illuminate\Support\Str::random(32),
            'has_entered_interests' => false,
            'has_viewed_assignment' => false,
        ];
    }

    public function withInterests(): static
    {
        return $this->has_entered_interests(true);
    }

    public function hasViewed(): static
    {
        return $this->has_viewed_assignment(true);
    }
}
