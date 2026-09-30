<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->hasAdminAccess()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized admin console access.'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'অ্যাডমিন প্যানেলে প্রবেশের অনুমতি আপনার নেই।');
        }

        return $next($request);
    }
}
