<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParticipantInterest>
 */
class ParticipantInterestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $interests = [
            'Coffee',
            'Reading books',
            'Chocolate',
            'Candles',
            'Board games',
            'Hiking',
            'Cooking',
            'Music',
            'Art supplies',
            'Tech gadgets',
            'Plants',
            'Tea',
            'Wine',
            'Craft beer',
            'Puzzles',
            'Photography',
        ];

        return [
            'participant_id' => \App\Models\Participant::factory(),
            'interest_text' => fake()->randomElement($interests),
        ];
    }
}
