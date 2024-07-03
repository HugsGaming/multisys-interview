<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login.
     *
     * @return void
     */
    public function test_successful_login()
    {
        User::factory()->create([
            "email"=> "test@example.com",
            "password"=> bcrypt("password123"),
        ]);

        $response = $this->postJson('/api/login', [
            'email'=> 'test@example.com',
            'password'=> 'password123',
        ]);

        $response->assertStatus(201)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);

    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function test_failed_login()
    {
        User::factory()->create([
            "email"=> "test@example.com",
            "password"=> bcrypt("password123"),
        ]);

        $response = $this->postJson("/api/login", [
            "email"=> "test@example.com",
            "password"=> "wrong-password",
        ]);

        $response->assertStatus(401)
        ->assertJson([
            "message"=> "Invalid credentials"
        ]);
    }
    
}
