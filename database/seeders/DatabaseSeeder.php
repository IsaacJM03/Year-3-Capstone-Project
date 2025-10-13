<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Donation;
use App\Models\DonationClaim;
use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@dra.com',
            'password' => Hash::make('password'),
        ]);

        // Create 3 Donors
        $donor1 = User::factory()->donor()->create([
            'name' => 'Kampala Supermarket',
            'email' => 'donor1@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'Kampala Supermarket',
            'verified' => true,
        ]);

        $donor2 = User::factory()->donor()->create([
            'name' => 'Garden City Restaurant',
            'email' => 'donor2@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'Garden City Restaurant',
            'verified' => true,
        ]);

        $donor3 = User::factory()->donor()->create([
            'name' => 'Acacia Mall Food Court',
            'email' => 'donor3@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'Acacia Mall Food Court',
            'verified' => true,
        ]);

        // Create 3 Receivers
        $receiver1 = User::factory()->receiver()->create([
            'name' => 'Hope Children Home',
            'email' => 'receiver1@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'Hope Children Home',
            'verified' => true,
        ]);

        $receiver2 = User::factory()->receiver()->create([
            'name' => 'St. Mary\'s Charity',
            'email' => 'receiver2@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'St. Mary\'s Charity',
            'verified' => true,
        ]);

        $receiver3 = User::factory()->receiver()->create([
            'name' => 'Compassion Orphanage',
            'email' => 'receiver3@dra.com',
            'password' => Hash::make('password'),
            'organization' => 'Compassion Orphanage',
            'verified' => true,
        ]);

        // Create 5 Donations
        Donation::factory()->create([
            'donor_id' => $donor1->id,
            'title' => 'Fresh Bread and Pastries',
            'description' => 'End of day fresh bread and pastries',
            'category' => 'Bakery',
            'quantity' => 50,
            'unit' => 'packs',
            'status' => 'available',
        ]);

        Donation::factory()->create([
            'donor_id' => $donor2->id,
            'title' => 'Cooked Rice and Beans',
            'description' => 'Surplus cooked meals from lunch service',
            'category' => 'Cooked Food',
            'quantity' => 30,
            'unit' => 'portions',
            'status' => 'available',
        ]);

        Donation::factory()->create([
            'donor_id' => $donor3->id,
            'title' => 'Fresh Vegetables',
            'description' => 'Assorted fresh vegetables',
            'category' => 'Vegetables',
            'quantity' => 25,
            'unit' => 'kg',
            'status' => 'available',
        ]);

        Donation::factory()->create([
            'donor_id' => $donor1->id,
            'title' => 'Canned Goods',
            'description' => 'Canned beans, tomatoes, and soup',
            'category' => 'Canned Goods',
            'quantity' => 100,
            'unit' => 'items',
            'status' => 'available',
        ]);

        Donation::factory()->create([
            'donor_id' => $donor2->id,
            'title' => 'Fresh Fruits',
            'description' => 'Bananas, apples, and oranges',
            'category' => 'Fruits',
            'quantity' => 40,
            'unit' => 'kg',
            'status' => 'available',
        ]);

        // Create a few sample campaigns
        Campaign::factory()->count(2)->create([
            'creator_id' => $receiver1->id,
        ]);
    }
}
