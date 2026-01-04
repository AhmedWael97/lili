<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_request_id',
        'business_name',
        'website',
        'facebook_url',
        'facebook_handle',
        'instagram_handle',
        'twitter_handle',
        'linkedin_url',
        'address',
        'phone',
        'category',
        'relevance_score',
        // Legacy fields
        'brand_id',
        'name',
        'social_profiles',
        'positioning',
        'messaging',
        'pricing_signals',
        'channels',
        'seo_data',
        'content_strategy',
        'strengths',
        'weaknesses',
        'analyzed_at',
    ];

    protected $casts = [
        'social_profiles' => 'array',
        'positioning' => 'array',
        'messaging' => 'array',
        'pricing_signals' => 'array',
        'channels' => 'array',
        'seo_data' => 'array',
        'content_strategy' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'analyzed_at' => 'datetime',
    ];

    /**
     * Get the research request that owns the competitor.
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    /**
     * Get the social metrics for the competitor.
     */
    public function socialMetrics(): HasMany
    {
        return $this->hasMany(CompetitorSocialMetric::class);
    }

    /**
     * Get the posts for the competitor.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(CompetitorPost::class);
    }

    /**
     * Get the social intelligence for the competitor.
     */
    public function socialIntelligence(): HasOne
    {
        return $this->hasOne(SocialIntelligence::class);
    }

    /**
     * Get the feedbacks for the competitor.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(CompetitorFeedback::class);
    }

    /**
     * Get social metric for a specific platform.
     */
    public function getMetricForPlatform(string $platform)
    {
        return $this->socialMetrics()->where('platform', $platform)->first();
    }

    /**
     * Check if competitor has any social media presence.
     */
    public function hasSocialPresence(): bool
    {
        return $this->facebook_handle || 
               $this->instagram_handle || 
               $this->twitter_handle || 
               $this->linkedin_url;
    }

    // Legacy relationships
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(CompetitorKeyword::class);
    }

    public function organicKeywords(): HasMany
    {
        return $this->hasMany(CompetitorKeyword::class)->where('type', 'organic');
    }

    public function paidKeywords(): HasMany
    {
        return $this->hasMany(CompetitorKeyword::class)->where('type', 'paid');
    }

    public function backlinks(): HasMany
    {
        return $this->hasMany(CompetitorBacklink::class);
    }

    public function socialProfiles(): HasMany
    {
        return $this->hasMany(CompetitorSocialProfile::class);
    }

    /**
     * Check if competitor data needs refresh
     */
    public function needsRefresh(): bool
    {
        return !$this->analyzed_at || 
               $this->analyzed_at->diffInDays(now()) > 7;
    }
}
