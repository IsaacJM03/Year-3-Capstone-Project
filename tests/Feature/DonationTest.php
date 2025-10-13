<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test donor can create donation
     */
    public function test_donor_can_create_donation(): void
    {
        $donor = User::factory()->donor()->create();
        Passport::actingAs($donor);

        $response = $this->postJson('/api/v1/donations', [
            'title' => 'Test Donation',
            'description' => 'Test Description',
            'category' => 'Bakery',
            'quantity' => 10,
            'unit' => 'packs',
            'expiry_date' => now()->addDays(7)->format('Y-m-d'),
            'pickup_location' => 'Kampala',
            'latitude' => 0.3476,
            'longitude' => 32.5825,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test receiver cannot create donation
     */
    public function test_receiver_cannot_create_donation(): void
    {
        $receiver = User::factory()->receiver()->create();
        Passport::actingAs($receiver);

        $response = $this->postJson('/api/v1/donations', [
            'title' => 'Test Donation',
            'description' => 'Test Description',
            'category' => 'Bakery',
            'quantity' => 10,
            'unit' => 'packs',
            'expiry_date' => now()->addDays(7)->format('Y-m-d'),
            'pickup_location' => 'Kampala',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test can list donations
     */
    public function test_can_list_donations(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Donation::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/donations');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
