<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $goalAmount = fake()->numberBetween(1000, 50000);
        $raisedAmount = fake()->numberBetween(0, $goalAmount);
        
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'creator_id' => \App\Models\User::factory(),
            'goal_amount' => $goalAmount,
            'raised_amount' => $raisedAmount,
            'deadline' => fake()->dateTimeBetween('+7 days', '+90 days'),
            'status' => 'active',
        ];
    }
}
