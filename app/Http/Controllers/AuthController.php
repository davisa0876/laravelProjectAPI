<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user using RegisterRequest for validation.
     */
    public function register(RegisterRequest $request)
    {
        try {
            Log::info('Register data:', $request->all());
            $validatedData = $request->validated();
            $result = $this->authService->register($validatedData);

            return response()->json($result, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Registration error:', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Login an existing user using LoginRequest for validation.
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $result = $this->authService->login($credentials);
            
            return response()->json($result, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Login error:', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return the current authenticated user.
     */
    public function user()
    {
        return response()->json($this->authService->getUser(), Response::HTTP_OK);
    }

    /**
     * Logout the current user (revoke the token).
     */
    public function logout()
    {
        try {
            $this->authService->logout();
            return response()->noContent();
        } catch (\Exception $e) {
            Log::error('Logout error:', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update user profile information
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $this->authService->updateProfile($request->user(), $validated);

            return response()->json([
                'message' => __('auth.profile.update_success'),
                'user' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('auth.profile.update_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('auth.profile.update_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
