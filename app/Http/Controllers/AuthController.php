<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    /** 
     * Show the login page.
     */
    public function showLogin()
    {
        return Inertia::render('auth/Login');   
    }

    /**
     * Handle Login Submission
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    /**
     * Show the registration page.
     */
    public function showRegister()
    {
        return Inertia::render('auth/Register');
    } 

    /**
     * Handle registration submission
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        // Create the User account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Handshake: Automatically creates the Guest profile
        Guest::create([
            'name'  => $user->name,
            'email' => $user->email,
            'type'  => 'online'
        ]);

        // Log the user in automatically into the dashboard
        Auth::login($user);

        // Redirect them to the Dashboard
        return redirect()->intended('/');
    }

    /**
     * Handle Log-Out
     */
    public function logout(Request $request)
    {
        // Clears the timestamp before the user logs out from the website
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update([ 'last_seen_at' => null ]);
        }

        // Performs the Logout
        Auth::logout();

        // Clear the session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to home page
        return redirect('/');
    }
}