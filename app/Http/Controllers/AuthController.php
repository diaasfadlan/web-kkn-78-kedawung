<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class AuthController extends Controller
{
    protected mixed $auth;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Sign in using Firebase client-side SDK or via Guzzle to Firebase Identity Toolkit REST API
            // Because PHP Admin SDK does not support sign-in with password directly (only token generation/verification),
            // we sign in via Firebase Auth REST API (signInWithPassword).
            $signInResult = $this->auth->signInWithEmailAndPassword($request->email, $request->password);
            
            $idToken = $signInResult->idToken();
            
            $request->session()->put('firebase_token', $idToken);

            return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Email atau password salah / Gagal melakukan otentikasi.'
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('firebase_token');
        return redirect()->route('login')->with('success', 'Berhasil logout');
    }
}
