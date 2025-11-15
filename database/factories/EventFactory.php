<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true).' Gift Exchange',
            'description' => fake()->sentence(),
            'event_date' => fake()->dateTimeBetween('now', '+2 months'),
            'event_time' => fake()->time('H:i'),
            'max_gift_amount' => fake()->randomElement([2500, 3000, 5000, 10000]), // $25, $30, $50, $100
            'event_token' => \Illuminate\Support\Str::random(32),
            'theme' => fake()->randomElement(['default', 'winter', 'christmas', 'valentine']),
            'drawing_completed_at' => null,
        ];
    }
}
