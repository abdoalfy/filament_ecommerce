<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'message' => 'User login successfully',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


    public function userProfile() {
        $user = auth()->user();
        return response()->json([
            'status' => 'success',
            'message' => 'User profile successfully',
            'data' => new UserResource($user),
        ]);
    }



    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
            'data' =>[],
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'token refreshed Successfully',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function updateProfile(Request $request){
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email'    => [
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'name'     => 'string',
            'phone'    => [
                'nullable', // If phone is not required, add nullable
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'profile_photo_path' => 'nullable|image', // Make it nullable in case no photo is uploaded
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }
    
        // Get the authenticated user
        $user = auth()->user();
    
        // Handle profile photo upload if present
        if ($request->hasFile('profile_photo_path')) {
            // Store the image and get the path
            $path = $request->file('profile_photo_path')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }
    
        // If the user has provided a password, hash and update it
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
    
        // Update other user details
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->phone = $request->input('phone', $user->phone);
        $user->lastname = $request->input('lastname', $user->lastname);
    
        // Save the changes
        $user->save();
    
        // Return the updated user data
        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user),
        ]);
    }
}
