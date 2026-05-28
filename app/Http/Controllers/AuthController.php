<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:manager,tenant',
            'phone'    => 'required|string|max:20',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->phone = $request->phone;
        
        $prefix = match($request->role) {
            'tenant' => 'T',
            'manager' => 'M',
            'superadmin' => 'SA',
            default => 'U'
        };
        $user->user_code = $prefix . '-' . strtoupper(Str::random(6));

        if ($request->role === 'manager') {
            $user->referral_code = strtoupper(Str::random(6));
            $user->manager_status = 'pending';
        }

        $user->save();

        Auth::login($user);

        return $this->redirectBasedOnRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'manager':
                return redirect()->route('manager.dashboard');
            case 'tenant':
                return redirect()->route('tenant.dashboard');
            default:
                return redirect('/');
        }
    }
}
