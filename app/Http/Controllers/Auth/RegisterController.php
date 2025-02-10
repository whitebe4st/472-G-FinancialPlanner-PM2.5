<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('html.auth.register');
    }

    public function register(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Registration attempt with data:', $request->all());

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user
        try {
            \Log::info('Attempting to create user with:', [
                'username' => $request->username,
                'email' => $request->email
            ]);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
            ]);

            \Log::info('User created successfully:', ['user_id' => $user->user_id]);

            // Log the user in
            auth()->login($user);

            return redirect('/transaction');

        } catch (\Exception $e) {
            \Log::error('Registration failed with error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }
} 