<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Repositories\AuditLogRepository;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepo,
        protected UserRepository $userRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Package configurations
     */
    public function getPackageConfig(string $packageName): array
    {
        $packages = [
            'free' => [
                'name' => 'free',
                'price' => 0,
                'limits' => [
                    'facebook_pages_limit' => 1,
                    'posts_per_month_limit' => 10,
                    'comment_replies_limit' => 50,
                    'messages_limit' => 0,
                    'ad_campaigns_enabled' => false,
                    'ad_spend_limit' => 0,
                ],
            ],
            'starter' => [
                'name' => 'starter',
                'price' => 29,
                'limits' => [
                    'facebook_pages_limit' => 3,
                    'posts_per_month_limit' => 100,
                    'comment_replies_limit' => 500,
                    'messages_limit' => 100,
                    'ad_campaigns_enabled' => false,
                    'ad_spend_limit' => 0,
                ],
            ],
            'professional' => [
                'name' => 'professional',
                'price' => 99,
                'limits' => [
                    'facebook_pages_limit' => 10,
                    'posts_per_month_limit' => 500,
                    'comment_replies_limit' => -1, // unlimited
                    'messages_limit' => -1, // unlimited
                    'ad_campaigns_enabled' => true,
                    'ad_spend_limit' => 5000,
                ],
            ],
            'agency' => [
                'name' => 'agency',
                'price' => 299,
                'limits' => [
                    'facebook_pages_limit' => -1, // unlimited
                    'posts_per_month_limit' => -1, // unlimited
                    'comment_replies_limit' => -1, // unlimited
                    'messages_limit' => -1, // unlimited
                    'ad_campaigns_enabled' => true,
                    'ad_spend_limit' => 0, // unlimited
                ],
            ],
        ];

        return $packages[$packageName] ?? $packages['free'];
    }

    /**
     * Create subscription for user
     */
    public function createSubscription(int $userId, string $packageName, ?string $stripeSubscriptionId = null): Subscription
    {
        $config = $this->getPackageConfig($packageName);

        return DB::transaction(function () use ($userId, $config, $stripeSubscriptionId) {
            $subscription = $this->subscriptionRepo->createWithLimits(
                [
                    'user_id' => $userId,
                    'package_name' => $config['name'],
                    'price' => $config['price'],
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => now()->addMonth(),
                    'stripe_subscription_id' => $stripeSubscriptionId,
                ],
                $config['limits']
            );

            $this->auditRepo->log(
                'subscription_created',
                "User subscribed to {$config['name']} package",
                $userId,
                'Subscription',
                $subscription->id
            );

            return $subscription;
        });
    }

    /**
     * Upgrade subscription
     */
    public function upgradeSubscription(int $userId, string $newPackage): Subscription
    {
        $currentSubscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if (!$currentSubscription) {
            throw new \Exception('No active subscription found');
        }

        return DB::transaction(function () use ($userId, $newPackage, $currentSubscription) {
            // Cancel current subscription
            $this->subscriptionRepo->cancel($currentSubscription->id);

            // Create new subscription
            $newSubscription = $this->createSubscription($userId, $newPackage);

            $this->auditRepo->log(
                'subscription_upgraded',
                "Upgraded from {$currentSubscription->package_name} to {$newPackage}",
                $userId,
                'Subscription',
                $newSubscription->id
            );

            return $newSubscription;
        });
    }

    /**
     * Downgrade subscription
     */
    public function downgradeSubscription(int $userId, string $newPackage): Subscription
    {
        return $this->upgradeSubscription($userId, $newPackage);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(int $userId, string $reason = ''): bool
    {
        $subscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if (!$subscription) {
            throw new \Exception('No active subscription found');
        }

        $result = $this->subscriptionRepo->cancel($subscription->id);

        if ($result) {
            $this->auditRepo->log(
                'subscription_cancelled',
                "Subscription cancelled. Reason: {$reason}",
                $userId,
                'Subscription',
                $subscription->id,
                ['reason' => $reason]
            );
        }

        return $result;
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(int $userId): Subscription
    {
        $subscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if (!$subscription) {
            throw new \Exception('No active subscription found');
        }

        $subscription->update([
            'expires_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        $this->auditRepo->log(
            'subscription_renewed',
            "Subscription renewed for {$subscription->package_name}",
            $userId,
            'Subscription',
            $subscription->id
        );

        return $subscription->fresh();
    }

    /**
     * Get user's subscription with limits
     */
    public function getUserSubscriptionWithLimits(int $userId): ?Subscription
    {
        $subscription = $this->subscriptionRepo->getActiveSubscription($userId);
        
        if ($subscription) {
            $subscription->load('usageLimits');
        }

        return $subscription;
    }

    /**
     * Check if user can access feature
     */
    public function canAccessFeature(int $userId, string $feature): bool
    {
        $subscription = $this->getUserSubscriptionWithLimits($userId);
        
        if (!$subscription) {
            return false;
        }

        $limits = $subscription->usageLimits;

        return match ($feature) {
            'ad_campaigns' => $limits->ad_campaigns_enabled,
            'unlimited_posts' => $limits->posts_per_month_limit === -1,
            'unlimited_messages' => $limits->messages_limit === -1,
            default => false,
        };
    }
}
