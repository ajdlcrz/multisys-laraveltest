<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function successful_login()
    {
        // Create a user
        $user = User::create([
            'name' => 'Backend',
            'email' => 'backend@multisyscorp.com',
            'password' => Hash::make('test123'), // Store the hashed password
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['access_token']);
        $this->assertArrayHasKey('access_token', $response->json());
    }

    /** @test */
    public function unsuccessful_login_due_to_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'backend123123@multisyscorp.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(["message" => "Invalid Credentials"]);
    }

    /** @test */
    public function account_locks_after_failed_attempts()
    {
        $user = User::create([
            'name' => 'Backend',
            'email' => 'backend@multisyscorp.com',
            'password' => Hash::make('test123'),
        ]);

        // Simulate 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'backend@multisyscorp.com',
                'password' => 'wrongpassword',
            ]);
            $response->assertStatus(401);
        }

        // Attempt to log in again (this should be locked)
        $response = $this->postJson('/api/login', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(401); // Assume 423 Locked
        $response->assertJson(["message" => "Too many attempts. Please try again in 300 seconds."]);
    }
}
