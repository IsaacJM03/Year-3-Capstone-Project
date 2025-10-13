<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Bakery', 'Fruits', 'Vegetables', 'Canned Goods', 'Dairy', 'Meat', 'Beverages'];
        $units = ['kg', 'packs', 'items', 'liters', 'pieces'];
        
        return [
            'donor_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'quantity' => fake()->numberBetween(1, 100),
            'unit' => fake()->randomElement($units),
            'expiry_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status' => 'available',
            'pickup_location' => fake()->address(),
            'latitude' => fake()->latitude(0, 2),  // Uganda latitude range
            'longitude' => fake()->longitude(29, 35),  // Uganda longitude range
            'image_url' => fake()->optional()->imageUrl(640, 480, 'food'),
        ];
    }
}
