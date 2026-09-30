<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request and check granular permission.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasAdminAccess()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'প্রবেশাধিকার নেই। অনুগ্রহ করে লগইন করুন।');
        }

        if (!$user->canDo($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'এই অ্যাকশন বা সেকশনে আপনার অনুমতি নেই।'], 403);
            }
            return redirect()->route('admin.dashboard')
                ->with('error', 'দুঃখিত, এই সেকশনে প্রবেশের জন্য আপনার অ্যাকাউন্টের অনুমতি নেই।');
        }

        return $next($request);
    }
}
