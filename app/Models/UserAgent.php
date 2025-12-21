<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAgent extends Model
{
    protected $fillable = [
        'user_id',
        'agent_type_id',
        'status',
        'activated_at',
        'last_used_at',
        'interaction_count',
        'settings',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_used_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent type
     */
    public function agentType(): BelongsTo
    {
        return $this->belongsTo(AgentType::class);
    }

    /**
     * Update last used timestamp
     */
    public function markAsUsed(): void
    {
        $this->update([
            'last_used_at' => now(),
            'interaction_count' => $this->interaction_count + 1,
        ]);
    }

    /**
     * Check if agent is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope for active agents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
