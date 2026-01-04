<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'competitor_id',
        'platform',
        'reviewer_name',
        'reviewer_role',
        'rating',
        'title',
        'review_text',
        'pros',
        'cons',
        'pain_points',
        'praise_points',
        'review_url',
        'review_date',
    ];

    protected $casts = [
        'rating' => 'float',
        'pain_points' => 'array',
        'praise_points' => 'array',
        'review_date' => 'date',
    ];

    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }
}
