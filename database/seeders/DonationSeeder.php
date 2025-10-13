<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\User;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donors = User::where('role', 'donor')->get();

        foreach ($donors as $donor) {
            // Create multiple donations for each donor
            Donation::create([
                'donor_id' => $donor->id,
                'title' => 'Fresh Vegetables',
                'description' => 'Assorted fresh vegetables including tomatoes, carrots, and lettuce',
                'food_type' => 'Vegetables',
                'quantity' => 50,
                'unit' => 'kg',
                'expiry_date' => now()->addDays(3),
                'pickup_address' => $donor->address,
                'pickup_latitude' => $donor->latitude,
                'pickup_longitude' => $donor->longitude,
                'status' => 'available',
            ]);

            Donation::create([
                'donor_id' => $donor->id,
                'title' => 'Cooked Meals',
                'description' => 'Ready-to-eat meals for 20 people',
                'food_type' => 'Cooked Food',
                'quantity' => 20,
                'unit' => 'servings',
                'expiry_date' => now()->addHours(6),
                'pickup_address' => $donor->address,
                'pickup_latitude' => $donor->latitude,
                'pickup_longitude' => $donor->longitude,
                'status' => 'available',
            ]);

            Donation::create([
                'donor_id' => $donor->id,
                'title' => 'Bread and Bakery Items',
                'description' => 'Fresh bread, pastries, and baked goods from today',
                'food_type' => 'Bakery',
                'quantity' => 30,
                'unit' => 'pieces',
                'expiry_date' => now()->addDays(2),
                'pickup_address' => $donor->address,
                'pickup_latitude' => $donor->latitude,
                'pickup_longitude' => $donor->longitude,
                'status' => 'available',
            ]);
        }
    }
}
