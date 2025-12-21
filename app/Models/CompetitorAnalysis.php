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
    
    /**
     * Get the data quality level
     */
    public function getDataQuality(): string
    {
        return $this->page_data['data_quality'] ?? 'unknown';
    }
    
    /**
     * Get the list of data sources used
     */
    public function getDataSources(): array
    {
        return $this->page_data['data_sources'] ?? [];
    }
    
    /**
     * Check if cross-validation is available
     */
    public function hasCrossValidation(): bool
    {
        return isset($this->page_data['cross_validation']) && 
               count($this->page_data['cross_validation']) > 1;
    }
    
    /**
     * Get data reliability information
     */
    public function getDataReliability(): ?array
    {
        return $this->page_data['data_reliability'] ?? null;
    }
    
    /**
     * Get a human-readable description of data sources
     */
    public function getDataSourcesSummary(): string
    {
        $sources = $this->getDataSources();
        
        if (empty($sources)) {
            return 'No data sources available';
        }
        
        $labels = [
            'facebook_api' => 'Facebook API',
            'social_blade' => 'Social Blade',
            'web_scraping' => 'Web Scraping',
        ];
        
        $sourceNames = array_map(fn($s) => $labels[$s] ?? $s, $sources);
        
        return count($sourceNames) === 1 
            ? $sourceNames[0]
            : implode(', ', array_slice($sourceNames, 0, -1)) . ' and ' . end($sourceNames);
    }
    
    /**
     * Get reliability badge color
     */
    public function getReliabilityBadgeColor(): string
    {
        $reliability = $this->getDataReliability();
        
        if (!$reliability) {
            return 'gray';
        }
        
        return match($reliability['status']) {
            'highly_reliable' => 'green',
            'reliable' => 'blue',
            'needs_verification' => 'yellow',
            default => 'gray',
        };
    }
}
