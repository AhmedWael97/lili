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
        'name',
        'website',
        'description',
        'location',
        'g2_url',
        'capterra_url',
        'trustpilot_url',
        'producthunt_url',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'overall_rating',
        'review_count',
        'relevance_score',
    ];

    protected $casts = [
        'overall_rating' => 'float',
        'review_count' => 'integer',
        'relevance_score' => 'integer',
    ];

    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompetitorReview::class);
    }

    public function pricing(): HasMany
    {
        return $this->hasMany(CompetitorPricing::class);
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

}