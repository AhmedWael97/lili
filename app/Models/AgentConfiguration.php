<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_agent_id',
        'agent_code',
        'business_name',
        'industry',
        'products_services',
        'unique_value_proposition',
        'competitors',
        'brand_colors',
        'brand_fonts',
        'brand_tone',
        'brand_personality',
        'brand_assets',
        'brand_story',
        'target_audience',
        'pain_points',
        'online_presence',
        'buying_motivations',
        'marketing_goals',
        'monthly_budget',
        'timeline',
        'key_metrics',
        'current_platforms',
        'existing_accounts',
        'whats_working',
        'whats_not_working',
        'content_types',
        'posting_frequency',
        'focus_keywords',
        'topics_to_avoid',
        'requires_approval',
        'contact_person',
        'communication_preference',
        'is_complete',
        'completed_at',
    ];

    protected $casts = [
        'brand_colors' => 'array',
        'brand_assets' => 'array',
        'target_audience' => 'array',
        'online_presence' => 'array',
        'marketing_goals' => 'array',
        'key_metrics' => 'array',
        'current_platforms' => 'array',
        'existing_accounts' => 'array',
        'content_types' => 'array',
        'focus_keywords' => 'array',
        'requires_approval' => 'boolean',
        'is_complete' => 'boolean',
        'completed_at' => 'datetime',
        'monthly_budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userAgent(): BelongsTo
    {
        return $this->belongsTo(UserAgent::class);
    }

    /**
     * Get formatted configuration for AI agent context
     */
    public function getContextPrompt(): string
    {
        $context = [];

        if ($this->business_name) {
            $context[] = "Business: {$this->business_name}";
            if ($this->industry) {
                $context[] = "Industry: {$this->industry}";
            }
        }

        if ($this->products_services) {
            $context[] = "Products/Services: {$this->products_services}";
        }

        if ($this->unique_value_proposition) {
            $context[] = "Unique Value: {$this->unique_value_proposition}";
        }

        if ($this->brand_tone) {
            $context[] = "Brand Tone: {$this->brand_tone}";
        }

        if ($this->target_audience) {
            $audience = $this->target_audience;
            $audienceStr = [];
            if (isset($audience['age'])) $audienceStr[] = "Age: {$audience['age']}";
            if (isset($audience['location'])) $audienceStr[] = "Location: {$audience['location']}";
            if (isset($audience['interests'])) $audienceStr[] = "Interests: {$audience['interests']}";
            if ($audienceStr) {
                $context[] = "Target Audience: " . implode(', ', $audienceStr);
            }
        }

        if ($this->marketing_goals) {
            $context[] = "Goals: " . implode(', ', $this->marketing_goals);
        }

        if ($this->focus_keywords) {
            $context[] = "Focus Keywords: " . implode(', ', $this->focus_keywords);
        }

        if ($this->topics_to_avoid) {
            $context[] = "AVOID Topics: {$this->topics_to_avoid}";
        }

        return implode("\n", $context);
    }

    /**
     * Check if configuration is complete enough to use
     */
    public function isUsable(): bool
    {
        return $this->is_complete && 
               !empty($this->business_name) && 
               !empty($this->target_audience);
    }
}
