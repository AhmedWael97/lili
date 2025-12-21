<?php

namespace App\Agents\Marketing;

use App\Agents\Base\BaseMarketingAgent;
use App\Services\AI\OpenAIService;

/**
 * Marketing Strategist Agent
 * 20 years of marketing strategy experience
 * Specializes in: Market analysis, campaign planning, content strategy
 */
class StrategistAgent extends BaseMarketingAgent
{
    protected string $agentName = 'Marketing Strategist';

    public function __construct(OpenAIService $openAI)
    {
        parent::__construct($openAI);
    }

    /**
     * Create content strategy
     */
    public function createStrategy(array $context): array
    {
        return $this->execute($context);
    }

    /**
     * Generate content calendar
     */
    public function generateContentCalendar(array $context, int $days = 7): array
    {
        $context['task_description'] = "Create a {$days}-day content calendar";
        
        $result = $this->createStrategy($context);
        
        return $result['content_calendar'] ?? [];
    }

    /**
     * Get system prompt from documentation
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a Senior Marketing Strategist AI Agent with {$this->experienceYears} years of experience.

Your expertise includes:
- Deep market analysis and competitive intelligence
- Consumer behavior and psychographic profiling
- Multi-channel marketing campaign planning
- Brand positioning and differentiation strategies
- Data-driven decision making and ROI optimization
- Industry trends and emerging marketing technologies

You act as a FULL MARKETING COMPANY, providing:
1. Comprehensive market research before any strategy
2. Detailed competitor analysis and positioning
3. Target audience segmentation and persona development
4. Strategic content calendars aligned with business goals
5. Budget allocation recommendations across channels
6. Performance metrics and KPIs for tracking success

Always respond in JSON format with structured recommendations, data-backed insights, and clear rationale for every decision.
PROMPT;
    }

    /**
     * Build user prompt with context
     */
    protected function buildPrompt(array $context): string
    {
        $brandContext = $this->buildBrandContext($context);
        $analyticsContext = $this->buildAnalyticsContext($context);
        
        $budgetInstruction = '';
        if (!empty($context['monthly_budget'])) {
            $budgetInstruction = "\n\nBudget Constraint: Suggest tactics that fit within the specified monthly budget. Provide cost-effective strategies for lead generation and customer acquisition.";
        }
        
        return <<<PROMPT
{$brandContext}

{$analyticsContext}

Task: {$context['task_description']}
{$budgetInstruction}

Provide strategic recommendations in JSON format with:
- content_calendar (array of posts with day, time, content_type, topic, objective, target_audience)
- strategic_recommendations (array of actionable suggestions with rationale)
- market_insights (key observations about the industry/target market)
- success_metrics (KPIs to track campaign performance)
PROMPT;
    }
}
