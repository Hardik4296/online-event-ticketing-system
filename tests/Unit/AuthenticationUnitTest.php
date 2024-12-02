<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthenticationUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration
     */
    public function test_register_success()
    {
        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('createUser')
                ->once()
                ->andReturn(true);
        });

        $response = $this->json('POST', route('auth.register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'phone_number' => '1234567890',
            'role' => 'attendee',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully',
            ]);
    }

    /**
     * Test login success
     */
    public function test_login_success()
    {

        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('login')
                ->once()
                ->andReturn(true);
        });

        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);
    }

    /**
     * Test login failure
     */
    public function test_login_failure()
    {
        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('login')
                ->once()
                ->andReturn(false);
        });

        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials',
            ]);
    }


    /**
     * Test logout success
     */
    public function test_logout_success()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->json('POST', route('auth.logout'));

        $response->assertRedirect('/')
            ->assertStatus(302);
    }
}
