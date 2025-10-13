<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonationClaim>
 */
class DonationClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donation_id' => \App\Models\Donation::factory(),
            'receiver_id' => \App\Models\User::factory(),
            'claim_status' => fake()->randomElement(['pending', 'approved', 'rejected', 'delivered']),
            'pickup_time' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
