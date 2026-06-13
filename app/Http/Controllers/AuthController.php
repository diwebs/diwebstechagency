<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is suspended.']);
            }

            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:student,client,candidate'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active'
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    public function devLogin(Request $request, $role)
    {
        // Find or create test user for the role
        $email = $role . '@diwebs.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => ucfirst($role) . ' Tester',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
                'status' => 'active'
            ]);
        }

        Auth::login($user);
        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('admin.dashboard');
            case 'client':
                return redirect()->route('portal.dashboard');
            case 'student':
                return redirect()->route('academy.dashboard');
            case 'candidate':
                return redirect()->route('cbt.dashboard');
            default:
                return redirect('/');
        }
    }
}
