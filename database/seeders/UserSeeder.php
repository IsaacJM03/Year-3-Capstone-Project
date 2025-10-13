<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@donation.app',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'address' => '123 Admin St, City',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        // Donor users
        User::create([
            'name' => 'Restaurant Donor',
            'email' => 'donor@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'phone' => '+1234567891',
            'address' => '456 Restaurant Ave, City',
            'latitude' => 40.7580,
            'longitude' => -73.9855,
        ]);

        User::create([
            'name' => 'Supermarket Donor',
            'email' => 'donor@supermarket.com',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'phone' => '+1234567892',
            'address' => '789 Market St, City',
            'latitude' => 40.7489,
            'longitude' => -73.9680,
        ]);

        // Receiver users
        User::create([
            'name' => 'Charity Organization',
            'email' => 'receiver@charity.org',
            'password' => Hash::make('password'),
            'role' => 'receiver',
            'phone' => '+1234567893',
            'address' => '321 Charity Rd, City',
            'latitude' => 40.7614,
            'longitude' => -73.9776,
        ]);

        User::create([
            'name' => 'Orphanage',
            'email' => 'receiver@orphanage.org',
            'password' => Hash::make('password'),
            'role' => 'receiver',
            'phone' => '+1234567894',
            'address' => '654 Care St, City',
            'latitude' => 40.7829,
            'longitude' => -73.9654,
        ]);
    }
}
