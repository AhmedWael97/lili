<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Market extends Model
{
    protected $fillable = [
        'brand_id',
        'industry',
        'country',
        'maturity_level',
        'search_volume',
        'trends',
        'opportunities',
        'risks',
        'seasonality',
        'analyzed_at',
    ];

    protected $casts = [
        'trends' => 'array',
        'opportunities' => 'array',
        'risks' => 'array',
        'seasonality' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Check if market data needs refresh
     */
    public function needsRefresh(): bool
    {
        return !$this->analyzed_at || 
               $this->analyzed_at->diffInDays(now()) > 30;
    }
}
