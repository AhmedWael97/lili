<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorSocialMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'platform',
        'followers',
        'following',
        'posts_count',
        'avg_engagement_rate',
        'posting_frequency',
        'last_post_date',
        'scraped_at',
    ];

    protected $casts = [
        'last_post_date' => 'date',
        'scraped_at' => 'datetime',
        'avg_engagement_rate' => 'decimal:2',
    ];

    /**
     * Get the competitor that owns the social metric.
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
