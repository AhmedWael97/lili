<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorSocialProfile extends Model
{
    protected $fillable = [
        'competitor_id',
        'platform',
        'profile_url',
        'username',
        'followers',
        'following',
        'posts_count',
        'engagement_rate',
        'avg_likes',
        'avg_comments',
        'posting_frequency',
        'content_themes',
        'top_posts',
        'last_scraped',
    ];

    protected $casts = [
        'followers' => 'integer',
        'following' => 'integer',
        'posts_count' => 'integer',
        'engagement_rate' => 'decimal:2',
        'avg_likes' => 'integer',
        'avg_comments' => 'integer',
        'content_themes' => 'array',
        'top_posts' => 'array',
        'last_scraped' => 'datetime',
    ];

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
