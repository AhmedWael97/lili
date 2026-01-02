<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'platform',
        'post_url',
        'post_text',
        'post_date',
        'likes',
        'comments',
        'shares',
        'engagement_rate',
        'content_type',
        'hashtags',
    ];

    protected $casts = [
        'post_date' => 'datetime',
        'hashtags' => 'array',
        'engagement_rate' => 'decimal:2',
    ];

    /**
     * Get the competitor that owns the post.
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Scope a query to order by engagement rate.
     */
    public function scopeHighestEngagement($query)
    {
        return $query->orderBy('engagement_rate', 'desc');
    }

    /**
     * Calculate total engagement.
     */
    public function getTotalEngagementAttribute(): int
    {
        return $this->likes + $this->comments + $this->shares;
    }
}
