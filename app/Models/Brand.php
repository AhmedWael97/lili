<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'website',
        'industry',
        'country',
        'currency',
        'description',
        'target_audience',
        'value_proposition',
        'products_services',
        'monthly_budget',
        'budget_tier',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'value_proposition' => 'array',
        'products_services' => 'array',
        'monthly_budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markets(): HasMany
    {
        return $this->hasMany(Market::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class);
    }

    public function strategyPlans(): HasMany
    {
        return $this->hasMany(StrategyPlan::class);
    }

    /**
     * Determine budget tier based on monthly budget
     */
    public function determineBudgetTier(): string
    {
        if (!$this->monthly_budget) {
            return 'small';
        }

        if ($this->monthly_budget < 500) {
            return 'small';
        } elseif ($this->monthly_budget < 5000) {
            return 'medium';
        } else {
            return 'large';
        }
    }
}
