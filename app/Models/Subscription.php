<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_name',
        'price',
        'status',
        'started_at',
        'expires_at',
        'stripe_subscription_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_name', 'name');
    }

    public function usageLimits(): HasOne
    {
        return $this->hasOne(UsageLimit::class);
    }

    /**
     * Get package features
     */
    public function getFeatures()
    {
        $package = Package::where('name', $this->package_name)->first();
        return $package ? $package->features : [];
    }

    /**
     * Helper methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
