<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->email_verified_at) {
            return redirect()->route('otp.show')
                ->with('error', 'Verifikasi email terlebih dahulu dengan kode OTP yang dikirim ke kotak masuk Anda.');
        }

        return $next($request);
    }
}
