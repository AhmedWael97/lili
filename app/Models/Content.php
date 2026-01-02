<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facebook_page_id',
        'agent_interaction_id',
        'title',
        'body',
        'image_url',
        'scheduled_at',
        'published_at',
        'status',
        'platform_post_id',
        'engagement_likes',
        'engagement_comments',
        'engagement_shares',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the content.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Facebook page associated with the content.
     */
    public function facebookPage()
    {
        return $this->belongsTo(FacebookPage::class);
    }

    /**
     * Get the agent interaction that created this content.
     */
    public function agentInteraction()
    {
        return $this->belongsTo(AgentInteraction::class);
    }

    /**
     * Scope a query to only include published content.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include scheduled content.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include draft content.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get total engagement for this content.
     */
    public function getTotalEngagementAttribute()
    {
        return ($this->engagement_likes ?? 0) + 
               ($this->engagement_comments ?? 0) + 
               ($this->engagement_shares ?? 0);
    }
}
