<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function successful_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Backend',
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            "message" => "User Successfully Registered"
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'backend@multisyscorp.com',
        ]);
    }

    /** @test */
    public function registration_if_email_already_taken()
    {
        // Create a user
        $user = User::create([
            'name' => 'Backend',
            'email' => 'backend@multisyscorp.com',
            'password' => Hash::make('test123'), // Store the hashed password
        ]);

        $response = $this->postJson('/api/register', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Email already taken',
        ]);
    }



    // /** @test */
    // public function account_locks_after_failed_attempts()
    // {
    //     $user = User::create([
    //         'email' => 'backend@multisyscorp.com',
    //         'password' => bcrypt('test123'),
    //     ]);

    //     // Simulate 5 failed login attempts
    //     for ($i = 0; $i < 5; $i++) {
    //         $response = $this->postJson('/api/login', [
    //             'email' => 'backend@multisyscorp.com',
    //             'password' => 'wrongpassword',
    //         ]);
    //         $response->assertStatus(422);
    //     }

    //     // Attempt to log in again (this should be locked)
    //     $response = $this->postJson('/api/login', [
    //         'email' => 'backend@multisyscorp.com',
    //         'password' => 'test123',
    //     ]);

    //     $response->assertStatus(423); // Assume 423 Locked
    //     $response->assertJson([
    //         'message' => 'Account locked. Try again later.',
    //     ]);
    // }
}
