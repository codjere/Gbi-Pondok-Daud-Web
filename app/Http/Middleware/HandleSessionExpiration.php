<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is an AJAX request and session is expired
        if ($request->ajax() || $request->wantsJson()) {
            if (! $request->session()->has('_token') && ! $request->session()->has('user_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali.',
                    'session_expired' => true
                ], 401);
            }
        }

        return $next($request);
    }
}
