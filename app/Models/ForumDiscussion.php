<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_request_id',
        'source',
        'url',
        'title',
        'content',
        'pain_points',
        'feature_requests',
        'upvotes',
        'comments_count',
        'posted_date',
    ];

    protected $casts = [
        'pain_points' => 'array',
        'feature_requests' => 'array',
        'upvotes' => 'integer',
        'comments_count' => 'integer',
        'posted_date' => 'date',
    ];

    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }
}
