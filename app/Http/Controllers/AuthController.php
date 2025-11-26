<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthUser;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // Cari user
        $user = AuthUser::where('username', $request->login)->first();

        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan.');
        }

        // Verifikasi password
        if (!password_verify($request->password, $user->passwordHash)) {
            return back()->with('error', 'Password salah.');
        }

        // Login ke Laravel
        Auth::login($user);

        // Redirect by role
        return $user->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('player.dashboard');
    }
}
