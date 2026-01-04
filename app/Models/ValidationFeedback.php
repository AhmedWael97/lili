<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationFeedback extends Model
{
    use HasFactory;

    protected $table = 'validation_feedback';

    protected $fillable = [
        'research_request_id',
        'user_id',
        'validation_type',
        'item_identifier',
        'system_score',
        'system_prediction',
        'user_verdict',
        'features',
        'correction_data',
        'validated_at',
    ];

    protected $casts = [
        'system_prediction' => 'boolean',
        'user_verdict' => 'boolean',
        'features' => 'array',
        'correction_data' => 'array',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the research request
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    /**
     * Get the user who provided feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the system prediction was correct
     */
    public function wasCorrect(): bool
    {
        if ($this->system_prediction === null || $this->user_verdict === null) {
            return false;
        }

        return $this->system_prediction === $this->user_verdict;
    }

    /**
     * Check if this was a false positive
     */
    public function isFalsePositive(): bool
    {
        return $this->system_prediction === true && $this->user_verdict === false;
    }

    /**
     * Check if this was a false negative
     */
    public function isFalseNegative(): bool
    {
        return $this->system_prediction === false && $this->user_verdict === true;
    }

    /**
     * Scope for correct predictions
     */
    public function scopeCorrect($query)
    {
        return $query->whereRaw('system_prediction = user_verdict')
            ->whereNotNull('system_prediction')
            ->whereNotNull('user_verdict');
    }

    /**
     * Scope for incorrect predictions
     */
    public function scopeIncorrect($query)
    {
        return $query->whereRaw('system_prediction != user_verdict')
            ->whereNotNull('system_prediction')
            ->whereNotNull('user_verdict');
    }
}
