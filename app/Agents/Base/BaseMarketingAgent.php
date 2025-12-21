<?php

namespace App\Agents\Base;

/**
 * Marketing Agent Base Class
 * All marketing-specific agents extend this
 */
abstract class BaseMarketingAgent extends BaseAgent
{
    protected string $agentType = 'marketing';

    /**
     * Build brand context for marketing tasks
     */
    protected function buildBrandContext(array $context): string
    {
        $language = $context['preferred_language'] ?? 'en';
        $languageName = $this->getLanguageName($language);
        
        $budgetInfo = '';
        if (!empty($context['monthly_budget'])) {
            $budgetInfo = "\n- Monthly Marketing Budget: $" . number_format($context['monthly_budget'], 2);
        }

        return <<<CONTEXT
Brand Context:
- Brand Name: {$context['brand_name']}
- Industry: {$context['industry']}
- Brand Tone: {$context['brand_tone']}
- Voice Characteristics: {$context['voice_characteristics']}
- Target Audience: {$context['target_audience']}
- Business Goals: {$context['business_goals']}
- Key Messages: {$context['key_messages']}
- Words to Avoid: {$context['forbidden_words']}{$budgetInfo}

IMPORTANT: Generate ALL content in {$languageName} language only.
CONTEXT;
    }

    /**
     * Build market analytics context
     */
    protected function buildAnalyticsContext(array $context): string
    {
        return <<<ANALYTICS
Market Analytics:
- Total Followers: {$context['follower_count']}
- Average Engagement Rate: {$context['engagement_rate']}%
- Top Performing Content Types: {$context['top_post_types']}
- Peak Activity Times: {$context['peak_times']}
ANALYTICS;
    }
}
