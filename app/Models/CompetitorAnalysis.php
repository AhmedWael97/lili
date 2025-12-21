<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'competitor_name',
        'facebook_page_id',
        'industry',
        'page_data',
        'engagement_metrics',
        'posting_patterns',
        'content_strategy',
        'last_analyzed_at',
    ];

    protected $casts = [
        'page_data' => 'array',
        'engagement_metrics' => 'array',
        'posting_patterns' => 'array',
        'content_strategy' => 'array',
        'last_analyzed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function needsRefresh(): bool
    {
        return !$this->last_analyzed_at || 
               $this->last_analyzed_at->diffInHours(now()) > 24;
    }
}
