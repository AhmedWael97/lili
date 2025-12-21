<?php

namespace App\Http\Middleware;

use App\Models\UsageTracking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_REQUIRED',
                    'message' => 'Authentication required',
                ],
            ], 401);
        }

        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SUBSCRIPTION_REQUIRED',
                    'message' => 'Active subscription required',
                    'details' => 'Please subscribe to a plan to use this feature',
                ],
            ], 403);
        }

        $subscription = $user->subscription;
        $usageLimits = $subscription->usageLimits;
        $currentUsage = $user->getCurrentUsage() ?? UsageTracking::getOrCreateForCurrentMonth($user->id);

        // Check specific limit type
        switch ($limitType) {
            case 'post':
                $limit = $usageLimits->posts_per_month_limit;
                $used = $currentUsage->posts_count;
                $limitName = 'posts';
                break;

            case 'comment_reply':
                $limit = $usageLimits->comment_replies_limit;
                $used = $currentUsage->comment_replies_count;
                $limitName = 'comment replies';
                break;

            case 'message':
                $limit = $usageLimits->messages_limit;
                $used = $currentUsage->messages_count;
                $limitName = 'messages';
                break;

            case 'facebook_page':
                $limit = $usageLimits->facebook_pages_limit;
                $used = $user->facebookPages()->where('status', 'active')->count();
                $limitName = 'Facebook pages';
                break;

            default:
                return $next($request);
        }

        // -1 means unlimited
        if ($limit !== -1 && $used >= $limit) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LIMIT_EXCEEDED',
                    'message' => ucfirst($limitName) . ' limit exceeded',
                    'details' => "You have used {$used}/{$limit} {$limitName} this month. Upgrade your plan or wait for next billing cycle.",
                    'current_usage' => $used,
                    'limit' => $limit,
                    'reset_date' => now()->addMonth()->startOfMonth()->format('Y-m-d'),
                ],
                'meta' => [
                    'upgrade_url' => route('dashboard.billing'),
                ],
            ], 403);
        }

        return $next($request);
    }
}
