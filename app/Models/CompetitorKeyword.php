<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorKeyword extends Model
{
    protected $fillable = [
        'competitor_id',
        'type',
        'keyword',
        'position',
        'search_volume',
        'cpc',
        'url',
        'traffic',
        'traffic_cost',
    ];

    protected $casts = [
        'position' => 'integer',
        'search_volume' => 'integer',
        'cpc' => 'decimal:2',
        'traffic' => 'integer',
        'traffic_cost' => 'decimal:2',
    ];

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
