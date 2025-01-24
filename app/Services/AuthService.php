<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    /**
     * Handle user registration logic.
     */
    public function register($validatedData)
    {
        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'], // Model will hash this
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            throw new \Exception('Registration failed, please try again later.');
        }
    }
    /**
     * Handle user login logic.
     */
    public function login($credentials)
    {
        try {
            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Login error:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Return the currently authenticated user (Sanctum).
     */
    public function getUser()
    {
        try {
            return Auth::user();
        } catch (Exception $e) {
            throw new Exception('Failed to get user: ' . $e->getMessage());
        }
    }

    /**
     * Logout (revoke current access token).
     */
    public function logout()
    {
        try {
            if (Auth::check() && Auth::user()->currentAccessToken()) {
                Auth::user()->tokens()->delete();
            }
        } catch (\Exception $e) {
            Log::error('Error during logout: ' . $e->getMessage());
            throw new \Exception('Logout failed, please try again later.');
        }
    }


    /**
     * Update user profile information
     */
    public function updateProfile($user, array $data)
    {
        try {
            $user->fill([
                'name' => $data['name'] ?? $user->name,
            ]);

            if (isset($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            return $user;
        } catch (Exception $e) {
            throw new Exception('Failed to update profile: ' . $e->getMessage());
        }
    }
}
