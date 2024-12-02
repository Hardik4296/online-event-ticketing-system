<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the login view.
     */
    public function loginView()
    {
        return redirect('/');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->userService->createUser($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
            ], 201);
        } catch (Exception $e) {
            Log::error( 'Error registering user',['message' =>$e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error registering user',
            ], 422);
        }
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $loginResult = $this->userService->login($validatedData);

            if ($loginResult) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        } catch (Exception $e) {
            Log::error( 'Error logging in',['message' =>$e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error logging in'
            ], 401);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        try {
            Auth::logout();

            return redirect('/');
        } catch (Exception $e) {
            Log::error( 'Error logging out',['message' =>$e->getMessage()]);
            abort(404);
        }
    }
}
