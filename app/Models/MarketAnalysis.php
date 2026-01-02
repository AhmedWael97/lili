<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketAnalysis extends Model
{
    use HasFactory;

    protected $table = 'market_analysis';

    protected $fillable = [
        'research_request_id',
        'market_size_estimate',
        'growth_rate',
        'competition_level',
        'target_audience',
        'trends',
        'opportunities',
        'threats',
        'barriers_to_entry',
        'ai_analysis',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'trends' => 'array',
        'opportunities' => 'array',
        'threats' => 'array',
        'barriers_to_entry' => 'array',
        'growth_rate' => 'decimal:2',
    ];

    /**
     * Get the research request that owns the market analysis.
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
