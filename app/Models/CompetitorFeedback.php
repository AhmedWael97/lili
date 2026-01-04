<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorFeedback extends Model
{
    use HasFactory;

    protected $table = 'competitor_feedback';

    protected $fillable = [
        'competitor_id',
        'research_request_id',
        'user_id',
        'feedback_type',
        'is_useful',
        'is_relevant',
        'is_accurate',
        'is_duplicate',
        'is_spam',
        'field_corrections',
        'overall_rating',
        'comments',
        'metadata',
        'verified_at',
    ];

    protected $casts = [
        'is_useful' => 'boolean',
        'is_relevant' => 'boolean',
        'is_accurate' => 'boolean',
        'is_duplicate' => 'boolean',
        'is_spam' => 'boolean',
        'field_corrections' => 'array',
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the competitor this feedback is for
     */
    public function competitor(): BelongsTo
    {
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get the research request this feedback is for
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    /**
     * Get the user who provided the feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get positive feedback
     */
    public function scopePositive($query)
    {
        return $query->where('is_useful', true)
            ->where('is_relevant', true)
            ->where('is_spam', false);
    }

    /**
     * Scope to get negative feedback
     */
    public function scopeNegative($query)
    {
        return $query->where(function ($q) {
            $q->where('is_useful', false)
                ->orWhere('is_relevant', false)
                ->orWhere('is_spam', true);
        });
    }

    /**
     * Get feedback summary
     */
    public function getSummaryAttribute(): array
    {
        return [
            'is_positive' => $this->is_useful && $this->is_relevant && !$this->is_spam,
            'rating' => $this->overall_rating,
            'has_corrections' => !empty($this->field_corrections),
            'feedback_type' => $this->feedback_type,
        ];
    }
}
