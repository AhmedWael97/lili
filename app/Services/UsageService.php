<?php

namespace App\Services;

use App\Repositories\UsageTrackingRepository;
use App\Repositories\SubscriptionRepository;
use App\Models\UsageTracking;

class UsageService
{
    public function __construct(
        protected UsageTrackingRepository $usageRepo,
        protected SubscriptionRepository $subscriptionRepo
    ) {}

    /**
     * Get current month usage for user
     */
    public function getCurrentUsage(int $userId): ?UsageTracking
    {
        return $this->usageRepo->getCurrentUsage($userId);
    }

    /**
     * Get or create current month usage
     */
    public function getOrCreateCurrentUsage(int $userId): UsageTracking
    {
        return $this->usageRepo->getOrCreateForMonth($userId);
    }

    /**
     * Track post creation
     */
    public function trackPost(int $userId): void
    {
        $this->usageRepo->incrementPosts($userId);
    }

    /**
     * Track comment reply
     */
    public function trackCommentReply(int $userId): void
    {
        $this->usageRepo->incrementCommentReplies($userId);
    }

    /**
     * Track message
     */
    public function trackMessage(int $userId): void
    {
        $this->usageRepo->incrementMessages($userId);
    }

    /**
     * Track ad spend
     */
    public function trackAdSpend(int $userId, float $amount): void
    {
        $this->usageRepo->addAdSpend($userId, $amount);
    }

    /**
     * Check if user has exceeded limit
     */
    public function hasExceededLimit(int $userId, string $limitType): array
    {
        $subscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if (!$subscription) {
            return [
                'exceeded' => true,
                'message' => 'No active subscription',
            ];
        }

        $limits = $subscription->usageLimits;
        $usage = $this->getCurrentUsage($userId);

        if (!$usage) {
            return [
                'exceeded' => false,
                'usage' => 0,
                'limit' => $this->getLimitValue($limits, $limitType),
            ];
        }

        $limit = $this->getLimitValue($limits, $limitType);
        $used = $this->getUsageValue($usage, $limitType);

        // -1 means unlimited
        if ($limit === -1) {
            return [
                'exceeded' => false,
                'usage' => $used,
                'limit' => -1,
                'unlimited' => true,
            ];
        }

        $exceeded = $used >= $limit;

        return [
            'exceeded' => $exceeded,
            'usage' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
            'percentage' => $limit > 0 ? round(($used / $limit) * 100, 2) : 0,
        ];
    }

    /**
     * Get usage summary with limits
     */
    public function getUsageSummary(int $userId): array
    {
        $subscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if (!$subscription) {
            return [
                'has_subscription' => false,
                'usage' => null,
            ];
        }

        $limits = $subscription->usageLimits;
        $usage = $this->getCurrentUsage($userId) ?? $this->getOrCreateCurrentUsage($userId);

        return [
            'has_subscription' => true,
            'package' => $subscription->package_name,
            'month_year' => $usage->month_year,
            'posts' => [
                'used' => $usage->posts_count ?? 0,
                'limit' => $limits->posts_per_month_limit,
                'unlimited' => $limits->posts_per_month_limit === -1,
                'percentage' => $this->calculatePercentage($usage->posts_count, $limits->posts_per_month_limit),
            ],
            'comment_replies' => [
                'used' => $usage->comment_replies_count ?? 0,
                'limit' => $limits->comment_replies_limit,
                'unlimited' => $limits->comment_replies_limit === -1,
                'percentage' => $this->calculatePercentage($usage->comment_replies_count, $limits->comment_replies_limit),
            ],
            'messages' => [
                'used' => $usage->messages_count ?? 0,
                'limit' => $limits->messages_limit,
                'unlimited' => $limits->messages_limit === -1,
                'percentage' => $this->calculatePercentage($usage->messages_count, $limits->messages_limit),
            ],
            'ad_spend' => [
                'used' => $usage->ad_spend_total ?? 0,
                'limit' => $limits->ad_spend_limit,
                'unlimited' => $limits->ad_spend_limit == 0 && $limits->ad_campaigns_enabled,
                'currency' => 'USD',
            ],
        ];
    }

    /**
     * Get usage history
     */
    public function getUsageHistory(int $userId, int $months = 6): array
    {
        $history = $this->usageRepo->getUserUsageHistory($userId, $months);

        return $history->map(function ($usage) {
            return [
                'month' => $usage->month_year,
                'posts' => $usage->posts_count,
                'comment_replies' => $usage->comment_replies_count,
                'messages' => $usage->messages_count,
                'ad_spend' => $usage->ad_spend_total,
            ];
        })->toArray();
    }

    /**
     * Reset usage for new billing cycle
     */
    public function resetUsage(int $userId): UsageTracking
    {
        return $this->usageRepo->resetMonthlyUsage($userId);
    }

    /**
     * Helper: Get limit value by type
     */
    protected function getLimitValue($limits, string $type): int
    {
        return match ($type) {
            'post' => $limits->posts_per_month_limit,
            'comment_reply' => $limits->comment_replies_limit,
            'message' => $limits->messages_limit,
            default => 0,
        };
    }

    /**
     * Helper: Get usage value by type
     */
    protected function getUsageValue($usage, string $type): int
    {
        $value = match ($type) {
            'post' => $usage->posts_count,
            'comment_reply' => $usage->comment_replies_count,
            'message' => $usage->messages_count,
            default => 0,
        };
        
        return $value ?? 0;
    }

    /**
     * Helper: Calculate percentage
     */
    protected function calculatePercentage(?int $used, ?int $limit): float
    {
        $used = $used ?? 0;
        $limit = $limit ?? 0;
        
        if ($limit === -1 || $limit === 0) {
            return 0;
        }

        return round(($used / $limit) * 100, 2);
    }
}
