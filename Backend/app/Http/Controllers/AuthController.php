<?php

// Include necessary namespaces
namespace App\Http\Controllers;

use Illuminate\Http\Request; // For handling HTTP requests
use Illuminate\Support\Facades\Auth; // For handling authentication
use App\Models\User; // User model

// Define AuthController class which extends Controller
class AuthController extends Controller
{
// Function to handle user registration
    public function register(Request $request)
    {
// Validate incoming request fields
        $request->validate([
            'name' => 'required|string|max:255', // Name must be a string, not exceed 255 characters and it is required
            'email' => 'required|string|email|max:255|unique:users', // Email must be a string, a valid email, not exceed 255 characters, it is required and it must be unique in the users table
            'password' => 'required|string|min:6', // Password must be a string, at least 6 characters and it is required
        ]);

// Create new User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
            'role' => 'user',
        ]);

// Create a new token for the user
        $token = $user->createToken('authToken')->plainTextToken;

// Return user data and token as JSON
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

// Function to handle user login
    public function login(Request $request)
    {
// Validate incoming request fields
        $request->validate([
            'email' => 'required|string|email', // Email must be a string, a valid email and it is required
            'password' => 'required|string', // Password must be a string and it is required
        ]);

// Check if the provided credentials are valid
        if (!Auth::attempt($request->only('email', 'password'))) {
// If not, return error message with a 401 (Unauthorized) HTTP status code
            return response()->json(['message' => 'Неверный логин или пароль'], 401);
        }

// If credentials are valid, get the authenticated user
        $user = $request->user();
// Create a new token for this user
        $token = $user->createToken('authToken')->plainTextToken;

// Return user data and token as JSON
        return response()->json(['user' => $user, 'token' => $token]);
    }

// Function to handle user logout
    public function logout(Request $request)
    {
        // Delete all tokens for the authenticated user
        $request->user()->tokens()->delete();

        // Return success message as JSON
        return response()->json(['message' => 'Logged out']);
    }
}
