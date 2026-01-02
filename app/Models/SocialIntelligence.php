<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialIntelligence extends Model
{
    use HasFactory;

    protected $table = 'social_intelligence';

    protected $fillable = [
        'competitor_id',
        'content_themes',
        'top_hashtags',
        'best_posting_times',
        'engagement_patterns',
        'strengths',
        'weaknesses',
        'ai_insights',
    ];

    protected $casts = [
        'content_themes' => 'array',
        'top_hashtags' => 'array',
        'engagement_patterns' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
    ];

    /**
     * Get the competitor that owns the social intelligence.
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
