<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrategyPlan extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'status',
        'swot_analysis',
        'positioning',
        'channel_strategy',
        'funnel_design',
        'budget_allocation',
        'content_themes',
        'messaging_pillars',
        'kpis',
        'execution_priorities',
        'risks_compliance',
        'generated_at',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'swot_analysis' => 'array',
        'positioning' => 'array',
        'channel_strategy' => 'array',
        'funnel_design' => 'array',
        'budget_allocation' => 'array',
        'content_themes' => 'array',
        'messaging_pillars' => 'array',
        'kpis' => 'array',
        'execution_priorities' => 'array',
        'risks_compliance' => 'array',
        'generated_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Check if strategy is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
