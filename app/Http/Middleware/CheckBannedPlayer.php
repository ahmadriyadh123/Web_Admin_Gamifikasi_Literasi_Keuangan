<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedPlayer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated and has is_active field
        if ($user && isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'error' => 'Account Banned',
                'message' => 'Your account has been banned. Reason: ' . ($user->ban_reason ?? 'Violation of terms'),
                'banned' => true
            ], 403);
        }

        return $next($request);
    }
}
