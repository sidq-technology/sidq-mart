<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyDemoMiddleware
{
    /**
     * Handle an incoming request.
     * Intercept and prevent write/modify actions for demo accounts.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isDemoAdmin()) {
            // Allow logout action
            if ($request->routeIs('admin.logout') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Block any state-modifying requests (POST, PUT, PATCH, DELETE)
            if (in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $message = '⚠️ এটি একটি ডেমো অ্যাকাউন্ট। এখানে কোনো তথ্য তৈরি, পরিবর্তন বা মুছে ফেলার অনুমতি নেই (Read-Only Mode)।';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 403);
                }

                return back()->with('error', $message);
            }
        }

        return $next($request);
    }
}
