<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name']
        ]);
    }

    public function test_user_can_login()
    {
        $password = 'password123';
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password
        ]);

        // Verify user exists
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }

    public function test_user_can_get_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/user');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson($user->toArray());
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'New Name',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/profile', $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['user']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name'
        ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->actingAs($user)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
} 