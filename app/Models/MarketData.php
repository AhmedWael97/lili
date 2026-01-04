<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketData extends Model
{
    use HasFactory;

    protected $table = 'market_data';

    protected $fillable = [
        'research_request_id',
        'market_size_estimate',
        'growth_rate',
        'market_maturity',
        'competition_level',
        'target_audience',
        'trends',
        'technology_trends',
        'barriers_to_entry',
        'market_overview',
    ];

    protected $casts = [
        'growth_rate' => 'float',
        'target_audience' => 'array',
        'trends' => 'array',
        'technology_trends' => 'array',
        'barriers_to_entry' => 'array',
    ];

    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
