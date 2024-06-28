<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Notifications\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    
    /** 
     * Test Registration with Valid Data
     * 
     * @return void
     */
    public function test_registration_with_valid_data()
    {
        Notification::fake();
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201)
            ->assertJson([
                'message' => "User created successfully",
            ]);
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, UserRegistered::class);
    }

    /**
     * Test Registration with Email Already Exists
     * 
     * @return void
     */
    public function test_registration_with_email_already_exists()
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(400)
        ->assertJsonValidationErrors(['email']);
    }
}
