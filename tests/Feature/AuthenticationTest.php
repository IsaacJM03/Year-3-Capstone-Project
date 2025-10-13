<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can access protected route
     */
    public function test_authenticated_user_can_access_user_endpoint(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
