<?php

namespace App\Repositories;

use App\Models\UsageTracking;

class UsageTrackingRepository extends BaseRepository
{
    public function __construct(UsageTracking $model)
    {
        parent::__construct($model);
    }

    /**
     * Get or create usage for current month
     */
    public function getOrCreateForMonth(int $userId, ?string $monthYear = null): UsageTracking
    {
        $monthYear = $monthYear ?? now()->format('Y-m');
        
        return $this->model->firstOrCreate([
            'user_id' => $userId,
            'month_year' => $monthYear,
        ]);
    }

    /**
     * Get user's current month usage
     */
    public function getCurrentUsage(int $userId): ?UsageTracking
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('month_year', now()->format('Y-m'))
            ->first();
    }

    /**
     * Get usage by user and month
     */
    public function getByUserAndMonth(int $userId, string $monthYear): ?UsageTracking
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('month_year', $monthYear)
            ->first();
    }

    /**
     * Get user's usage history
     */
    public function getUserUsageHistory(int $userId, int $months = 12)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('month_year', 'desc')
            ->limit($months)
            ->get();
    }

    /**
     * Increment posts count
     */
    public function incrementPosts(int $userId, int $amount = 1): void
    {
        $usage = $this->getOrCreateForMonth($userId);
        $usage->increment('posts_count', $amount);
    }

    /**
     * Increment comment replies
     */
    public function incrementCommentReplies(int $userId, int $amount = 1): void
    {
        $usage = $this->getOrCreateForMonth($userId);
        $usage->increment('comment_replies_count', $amount);
    }

    /**
     * Increment messages
     */
    public function incrementMessages(int $userId, int $amount = 1): void
    {
        $usage = $this->getOrCreateForMonth($userId);
        $usage->increment('messages_count', $amount);
    }

    /**
     * Add ad spend
     */
    public function addAdSpend(int $userId, float $amount): void
    {
        $usage = $this->getOrCreateForMonth($userId);
        $usage->increment('ad_spend_total', $amount);
    }

    /**
     * Reset monthly usage (for new billing cycle)
     */
    public function resetMonthlyUsage(int $userId): UsageTracking
    {
        return $this->getOrCreateForMonth($userId, now()->format('Y-m'));
    }
}
