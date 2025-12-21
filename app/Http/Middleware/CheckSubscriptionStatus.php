<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
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
            return $next($request);
        }

        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'SUBSCRIPTION_INACTIVE',
                        'message' => 'Active subscription required',
                        'details' => 'Your subscription is inactive or expired. Please renew or upgrade your plan.',
                    ],
                    'meta' => [
                        'billing_url' => route('dashboard.billing'),
                    ],
                ], 403);
            }

            return redirect()->route('dashboard.billing')
                ->with('error', 'Please activate or renew your subscription to continue.');
        }

        return $next($request);
    }
}
