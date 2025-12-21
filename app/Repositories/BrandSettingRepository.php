<?php

namespace App\Repositories;

use App\Models\BrandSetting;

class BrandSettingRepository extends BaseRepository
{
    public function __construct(BrandSetting $model)
    {
        parent::__construct($model);
    }

    /**
     * Get or create brand settings for user
     */
    public function getOrCreateForUser(int $userId): BrandSetting
    {
        return BrandSetting::firstOrCreate(
            ['user_id' => $userId],
            [
                'brand_tone' => 'professional',
                'logo_in_images' => false,
            ]
        );
    }

    /**
     * Update brand settings for user
     */
    public function updateForUser(int $userId, array $data): BrandSetting
    {
        $settings = $this->getOrCreateForUser($userId);
        $settings->update($data);
        return $settings->fresh();
    }

    /**
     * Get brand context for AI agents
     */
    public function getAIContext(int $userId): array
    {
        $settings = $this->getOrCreateForUser($userId);
        
        return [
            'brand_name' => $settings->brand_name ?? 'Your Brand',
            'industry' => $settings->industry ?? 'General',
            'brand_tone' => $settings->brand_tone,
            'voice_characteristics' => $settings->voice_characteristics ?? 'professional, engaging',
            'target_audience' => $settings->target_audience ?? 'General audience',
            'business_goals' => $settings->business_goals ?? 'Increase engagement',
            'key_messages' => $settings->key_messages ?? '',
            'forbidden_words' => $settings->forbidden_words ?? '',
            'primary_colors' => $settings->primary_colors ?? '#1877F2',
            'visual_style' => $settings->visual_style ?? 'modern, clean',
            'logo_usage' => $settings->logo_in_images ? 'include' : 'optional',
        ];
    }
}
