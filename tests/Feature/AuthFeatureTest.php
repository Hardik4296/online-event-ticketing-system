<?php

namespace Tests\Feature;

use App\Models\User;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper method to create a test user.
     */
    private function createTestUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Test user registration.
     */
    public function test_user_can_register()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password@1234',
            'password_confirmation' => 'Password@1234',
            'phone_number' => '1234567890',
            'role' => Arr::random(['attendee', 'organizer']),
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /**
     * Test registration validation fails with invalid data.
     */
    public function test_user_registration_fails_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'pass',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test user login succeeds.
     */
    public function test_user_can_login()
    {
        $user = $this->createTestUser([
            'password' => bcrypt('password'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login fails with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $payload = [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials',
            ]);

        $this->assertGuest();
    }

    /**
     * Test user logout succeeds.
     */
    public function test_user_can_logout()
    {
        $user = $this->createTestUser([
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('auth.logout'));

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
