<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'AUTH_REQUIRED',
                        'message' => 'Authentication required',
                    ],
                ], 401);
            }

            return redirect()->route('login');
        }

        if (!$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'ADMIN_REQUIRED',
                        'message' => 'Admin access required',
                        'details' => 'You do not have permission to access this resource',
                    ],
                ], 403);
            }

            abort(403, 'Admin access required');
        }

        return $next($request);
    }
}
