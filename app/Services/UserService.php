<?php

namespace App\Services;

use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $user;


    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Handle create user
     */
    public function createUser(array $validatedData)
    {
        return User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'attendee',
        ]);
    }

    /**
     * Handle user login
     */
    public function login(array $credentials)
    {
        return Auth::attempt($credentials);
    }
}
