<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Strategy extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'days',
        'content_calendar',
        'strategic_recommendations',
        'brand_context',
        'status',
        'content_generated',
        'content_total',
    ];

    protected $casts = [
        'content_calendar' => 'array',
        'strategic_recommendations' => 'array',
        'brand_context' => 'array',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
