<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'icon',
        'color',
        'is_active',
        'features',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get user agents using this type
     */
    public function userAgents(): HasMany
    {
        return $this->hasMany(UserAgent::class);
    }

    /**
     * Get interactions for this agent type
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(AgentInteraction::class);
    }

    /**
     * Scope for active agents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
