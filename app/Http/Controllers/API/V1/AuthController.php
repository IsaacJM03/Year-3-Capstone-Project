<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }
        
        // Revoke old tokens before issuing a new one (optional, but good practice for mobile)
        $user->tokens()->delete(); 

        // 1. Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken; 

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'access_token' => $token,
            'token_type' => 'Bearer',
            // Sanctum tokens don't have built-in expiry, but you can calculate an app-level expiry
            'expires_at' => now()->addWeeks(1)->toIso8601String(), 
        ]);
    }

    public function logout(Request $request)
    {
        // 2. Delete the token used for the current request
        $request->user()->currentAccessToken()->delete(); 

        return response()->json(['message' => 'Successfully logged out']);
    }
    
    // ...

//     /**
//      * Register a new user
//      */
//     public function register(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8',
//             'role' => 'required|in:donor,receiver,admin',
//             'phone' => 'nullable|string|max:20',
//             'address' => 'nullable|string',
//             'organization' => 'nullable|string|max:255',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation Error',
//                 'data' => $validator->errors()
//             ], 422);
//         }

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => $request->role,
//             'phone' => $request->phone,
//             'address' => $request->address,
//             'organization' => $request->organization,
//         ]);

//         $token = $user->createToken('auth_token')->accessToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'User registered successfully',
//             'data' => [
//                 'user' => $user,
//                 'token' => $token
//             ]
//         ], 201);
//     }

//     /**
//      * Login user
//      */
//     public function login(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'email' => 'required|email',
//             'password' => 'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation Error',
//                 'data' => $validator->errors()
//             ], 422);
//         }

//         $user = User::where('email', $request->email)->first();

//         if (!$user || !Hash::check($request->password, $user->password)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Invalid credentials',
//                 'data' => null
//             ], 401);
//         }

//         $token = $user->createToken('auth_token')->accessToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'Login successful',
//             'data' => [
//                 'user' => $user,
//                 'token' => $token
//             ]
//         ], 200);
//     }

//     /**
//      * Logout user
//      */
//     public function logout(Request $request)
//     {
//         $request->user()->token()->revoke();

//         return response()->json([
//             'success' => true,
//             'message' => 'Logout successful',
//             'data' => null
//         ], 200);
//     }

//     /**
//      * Get authenticated user
//      */
//     public function user(Request $request)
//     {
//         return response()->json([
//             'success' => true,
//             'message' => 'User retrieved successfully',
//             'data' => $request->user()
//         ], 200);
//     }
}
