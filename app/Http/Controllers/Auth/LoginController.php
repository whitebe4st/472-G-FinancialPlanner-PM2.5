<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('html.auth.login');
    }

    public function login(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Login attempt with:', [
            'email' => $request->email
        ]);

        // Validate the request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            \Log::info('User found:', [
                'exists' => $user ? 'yes' : 'no'
            ]);

            // Check if user exists and password matches
            if ($user && Hash::check($request->password, $user->password_hash)) {
                // Login successful
                Auth::login($user);
                
                \Log::info('Login successful for user:', [
                    'user_id' => $user->user_id,
                    'email' => $user->email
                ]);

                return redirect('/transaction');
            }

            \Log::error('Login failed: Invalid credentials');

            // Login failed
            return redirect()
                ->back()
                ->withErrors(['email' => 'Invalid email or password'])
                ->withInput($request->except('password'));

        } catch (\Exception $e) {
            \Log::error('Login exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Login failed: ' . $e->getMessage())
                ->withInput($request->except('password'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
} 