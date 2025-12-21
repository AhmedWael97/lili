<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user's active subscription
     */
    public function getActiveSubscription(int $userId): ?Subscription
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * Get all user subscriptions
     */
    public function getUserSubscriptions(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get subscriptions by package
     */
    public function getByPackage(string $packageName): Collection
    {
        return $this->model->where('package_name', $packageName)->get();
    }

    /**
     * Get active subscriptions count
     */
    public function getActiveSubscriptionsCount(): int
    {
        return $this->model->where('status', 'active')->count();
    }

    /**
     * Get expired subscriptions
     */
    public function getExpiredSubscriptions(): Collection
    {
        return $this->model
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();
    }

    /**
     * Get subscriptions expiring soon (within days)
     */
    public function getExpiringSoon(int $days = 7): Collection
    {
        return $this->model
            ->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays($days)])
            ->get();
    }

    /**
     * Cancel subscription
     */
    public function cancel(int $subscriptionId): bool
    {
        return $this->update($subscriptionId, [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Create subscription with usage limits
     */
    public function createWithLimits(array $subscriptionData, array $limitsData): Subscription
    {
        $subscription = $this->create($subscriptionData);
        
        $subscription->usageLimits()->create($limitsData);
        
        return $subscription->load('usageLimits');
    }

    /**
     * Get revenue by package
     */
    public function getRevenueByPackage(): Collection
    {
        return $this->model
            ->where('status', 'active')
            ->selectRaw('package_name, SUM(price) as total_revenue, COUNT(*) as subscriber_count')
            ->groupBy('package_name')
            ->get();
    }
}
