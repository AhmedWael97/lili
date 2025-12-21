<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLimit extends Model
{
    protected $fillable = [
        'subscription_id',
        'facebook_pages_limit',
        'posts_per_month_limit',
        'comment_replies_limit',
        'messages_limit',
        'ad_campaigns_enabled',
        'ad_spend_limit',
    ];

    protected $casts = [
        'ad_campaigns_enabled' => 'boolean',
        'ad_spend_limit' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Helper methods
     */
    public function isUnlimited(string $limitType): bool
    {
        $field = $limitType . '_limit';
        return $this->{$field} === -1;
    }

    public function hasReachedLimit(string $limitType, int $currentUsage): bool
    {
        $field = $limitType . '_limit';
        $limit = $this->{$field};
        
        if ($limit === -1) {
            return false; // Unlimited
        }
        
        return $currentUsage >= $limit;
    }
}
