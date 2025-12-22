<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competitor extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'website',
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
