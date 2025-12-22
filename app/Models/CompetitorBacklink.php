<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorBacklink extends Model
{
    protected $fillable = [
        'competitor_id',
        'source_url',
        'target_url',
        'anchor_text',
        'domain_rating',
        'url_rating',
        'link_type',
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'domain_rating' => 'integer',
        'url_rating' => 'integer',
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
    ];

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
