<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandSetting extends Model
{
    protected $fillable = [
        'user_id',
        'brand_name',
        'industry',
        'brand_tone',
        'voice_characteristics',
        'target_audience',
        'business_goals',
        'key_messages',
        'forbidden_words',
        'primary_colors',
        'secondary_color',
        'visual_style',
        'font_family',
        'logo_in_images',
        'logo_path',
        'website_url',
        'preferred_language',
        'monthly_budget',
        'image_style',
        'image_mood',
        'image_composition',
        'text_in_images',
        'preferred_elements',
        'avoid_elements',
        'image_aspect_ratio',
    ];

    protected $casts = [
        'logo_in_images' => 'boolean',
    ];

    /**
     * Get the user that owns the brand settings
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get forbidden words as array
     */
    public function getForbiddenWordsArrayAttribute(): array
    {
        return $this->forbidden_words 
            ? array_map('trim', explode(',', $this->forbidden_words))
            : [];
    }

    /**
     * Get primary colors as array
     */
    public function getPrimaryColorsArrayAttribute(): array
    {
        return $this->primary_colors 
            ? array_map('trim', explode(',', $this->primary_colors))
            : [];
    }
}
