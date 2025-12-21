<?php

namespace App\Http\Middleware;

use App\Models\UsageTracking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $usageType): Response
    {
        $response = $next($request);

        // Only track if request was successful
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $user = $request->user();

            if ($user) {
                $usage = UsageTracking::getOrCreateForCurrentMonth($user->id);

                switch ($usageType) {
                    case 'post':
                        $usage->incrementPosts();
                        break;

                    case 'comment_reply':
                        $usage->incrementCommentReplies();
                        break;

                    case 'message':
                        $usage->incrementMessages();
                        break;
                }
            }
        }

        return $response;
    }
}
