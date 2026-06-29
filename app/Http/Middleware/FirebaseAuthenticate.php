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
            $verifiedIdToken = $auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');

            // Store user info in request
            $request->attributes->set('firebase_user', [
                'uid' => $uid,
                'email' => $verifiedIdToken->claims()->get('email'),
                'name' => $verifiedIdToken->claims()->get('name'),
            ]);

            return $next($request);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Token tidak valid');
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
