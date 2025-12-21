<?php

namespace App\Services\AI;

class AdsAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Generate ad campaign
     */
    public function generateCampaign(array $context): array
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt);
    }

    /**
     * Get system prompt
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a Facebook Ads Specialist AI Agent focused on creating high-performing ad campaigns.

Your role is to:
1. Analyze campaign objectives and recommend ad strategies
2. Create compelling ad copy and creative briefs
3. Suggest audience targeting parameters
4. Recommend budget allocation and bidding strategies
5. Propose A/B testing variations
6. Monitor campaign performance and suggest optimizations
7. Flag campaigns that need human approval before launch

⚠️ IMPORTANT: All ad campaigns must be approved by the user before launch.

Always respond in JSON format with complete campaign recommendations.
PROMPT;
    }

    /**
     * Build user prompt
     */
    protected function buildPrompt(array $context): string
    {
        return <<<PROMPT
Campaign Context:
- Brand Name: {$context['brand_name']}
- Campaign Objective: {$context['campaign_objective']}
- Budget: ${$context['budget']}
- Duration: {$context['duration']}
- Target Audience: {$context['target_audience']}
- Product/Service: {$context['product_service']}
- USP: {$context['usp']}

Performance Data:
- Previous CTR: {$context['previous_ctr']}%
- Previous CPC: ${$context['previous_cpc']}
- Previous ROAS: {$context['previous_roas']}x
- Top Performing Ad Types: {$context['top_ad_types']}

Task: {$context['task_description']}

Generate campaign in JSON format with:
- campaign_name
- campaign_structure (ad_sets with targeting, budget, placement)
- ad_creatives (multiple ads with headlines, primary_text, cta_button, creative_brief)
- bidding_strategy
- optimization_goal
- ab_test_variations
- success_metrics (target_roas, target_ctr, target_cpc, expected_conversions)
- requires_approval (always true)
- approval_notes
- optimization_schedule
PROMPT;
    }
}
