<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_request_id',
        'customer_personas',
        'pain_points',
        'needs',
        'feature_requests',
        'buying_factors',
        'satisfaction_drivers',
        'common_complaints',
        'purchase_decision_process',
        'marketing_channels',
        'sentiment_summary',
    ];

    protected $casts = [
        'customer_personas' => 'array',
        'pain_points' => 'array',
        'needs' => 'array',
        'feature_requests' => 'array',
        'buying_factors' => 'array',
        'satisfaction_drivers' => 'array',
        'common_complaints' => 'array',
        'purchase_decision_process' => 'array',
        'marketing_channels' => 'array',
    ];

    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
