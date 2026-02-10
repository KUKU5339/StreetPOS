<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login page (with no-cache headers to ensure fresh CSRF tokens)
    public function showLogin() {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Handle login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Show register page (with no-cache headers to ensure fresh CSRF tokens)
    public function showRegister() {
        return response()
            ->view('auth.register')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Handle register
    public function register(Request $request) {
        try {
            if (\Illuminate\Support\Facades\App::environment('production')) {
                try {
                    \Illuminate\Support\Facades\DB::connection()->getPdo();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('db connect error: ' . $e->getMessage());
                }
                if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                }
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Auth::login($user);
            return redirect('/')->with('success', 'Account created!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('register error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Registration failed. Please check database configuration.'])->withInput();
        }
    }

    // Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
