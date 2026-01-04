<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorPricing extends Model
{
    use HasFactory;

    protected $table = 'competitor_pricing';

    protected $fillable = [
        'competitor_id',
        'tier_name',
        'price',
        'billing_period',
        'pricing_model',
        'features',
        'user_limit',
        'description',
        'is_popular',
        'currency',
    ];

    protected $casts = [
        'price' => 'float',
        'features' => 'array',
        'user_limit' => 'integer',
        'is_popular' => 'boolean',
    ];

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
