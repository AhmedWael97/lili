<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'billing_cycle',
        'stripe_plan_id',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get all subscriptions for this package
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_name', 'name');
    }
}
