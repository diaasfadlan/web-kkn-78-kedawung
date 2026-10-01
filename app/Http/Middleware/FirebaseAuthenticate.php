<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get token from Authorization header or from session/cookie
        $token = $this->getToken($request);

        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $auth = app('firebase.auth');
            $verifiedIdToken = null;

            try {
                $verifiedIdToken = $auth->verifyIdToken($token);
            } catch (\Throwable $e) {
                // Coba refresh token jika masa berlaku ID token (1 jam) telah habis
                $refreshToken = $request->session()->get('firebase_refresh_token');
                if ($refreshToken) {
                    $signInResult = $auth->signInWithRefreshToken($refreshToken);
                    $newToken = $signInResult->idToken();
                    $request->session()->put('firebase_token', $newToken);
                    if ($signInResult->refreshToken()) {
                        $request->session()->put('firebase_refresh_token', $signInResult->refreshToken());
                    }
                    $verifiedIdToken = $auth->verifyIdToken($newToken);
                } else {
                    throw $e;
                }
            }

            $uid = $verifiedIdToken->claims()->get('sub');

            // Store user info in request
            $request->attributes->set('firebase_user', [
                'uid' => $uid,
                'email' => $verifiedIdToken->claims()->get('email'),
                'name' => $verifiedIdToken->claims()->get('name'),
            ]);

            return $next($request);
        } catch (\Throwable) {
            $request->session()->forget(['firebase_token', 'firebase_refresh_token']);

            return redirect()->route('login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
    }

    /**
     * Get token from request
     */
    protected function getToken(Request $request): ?string
    {
        // Check Authorization header
        if ($request->hasHeader('Authorization')) {
            $header = $request->header('Authorization');
            if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
                return $matches[1];
            }
        }

        // Check from session or cookie
        return $request->session()->get('firebase_token') ?? 
               $request->cookie('firebase_token');
    }
}
