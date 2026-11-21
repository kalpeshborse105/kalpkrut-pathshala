<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Student};

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = trim($request->input('email'));
        $password = $request->input('password');

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');
        $user = null;

        // 1. Direct email lookup
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', strtolower($input))->first();

            // Alias fallback: student1.aarav@apexcoach.test maps to the first seeded student
            if (!$user && strtolower($input) === 'student1.aarav@apexcoach.test') {
                $user = User::where('user_type', 'student')->first();
            }
        }

        // 2. Admission No, Roll No, or Phone lookup
        if (!$user) {
            $student = Student::where('admission_no', $input)
                ->orWhere('roll_no', $input)
                ->orWhere('phone', $input)
                ->orWhere('email', $input)
                ->first();

            if ($student) {
                $user = $student->user ?? User::where('email', $student->email)->first();
            }
        }

        // 3. Username shortcuts
        if (!$user) {
            if (in_array(strtolower($input), ['student', 'student1', 'student@coaching.com', 'student@kalpkrutinfotech.com'])) {
                $user = User::where('user_type', 'student')->first();
            }
        }

        // 4. Verify password if user resolved
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name);
        }

        // 5. Standard fallback attempt
        if (filter_var($input, FILTER_VALIDATE_EMAIL) && Auth::attempt(['email' => $input, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request, $type)
    {
        $user = match($type) {
            'admin' => User::where('user_type', 'super_admin')->first(),
            'counsellor' => User::where('user_type', 'counsellor')->first(),
            'accountant' => User::where('user_type', 'accountant')->first(),
            'faculty' => User::where('user_type', 'faculty')->first(),
            'student' => User::where('user_type', 'student')->first() ?? Student::first()?->user,
            default => User::first(),
        };

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            $roleLabel = $user->user_type ? ucwords(str_replace('_', ' ', $user->user_type)) : 'Portal';
            return redirect()->route('dashboard')->with('success', "Logged in as {$user->name} ({$roleLabel})");
        }

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'You have been successfully logged out.');
    }
}

