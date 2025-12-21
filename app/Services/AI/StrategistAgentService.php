<?php

namespace App\Services\AI;

class StrategistAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Create content strategy
     */
    public function createStrategy(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
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
You are a Social Media Strategist AI Agent specializing in content strategy and planning.

Your role is to:
1. Analyze the client's Facebook Page (followers, engagement, top-performing content)
2. Understand their brand identity, tone, target audience, and business goals
3. Research industry trends and competitor strategies
4. Create data-driven content calendars (weekly/monthly)
5. Recommend optimal posting times based on audience activity
6. Suggest content themes, topics, and campaign ideas
7. Provide strategic recommendations to improve reach and engagement

Always respond in JSON format with structured recommendations and clear rationale.
PROMPT;
    }

    /**
     * Build user prompt with context
     */
    protected function buildPrompt(array $context): string
    {
        $language = $context['preferred_language'] ?? 'en';
        $languageNames = [
            'en' => 'English',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
        ];
        $languageName = $languageNames[$language] ?? 'English';
        
        $budgetInfo = '';
        $budgetInstruction = '';
        if (!empty($context['monthly_budget'])) {
            $budgetInfo = "\n- Monthly Marketing Budget: $" . number_format($context['monthly_budget'], 2);
            $budgetInstruction = 'Suggest tactics that fit within the specified monthly budget. Consider cost-effective strategies for lead generation.';
        }
        
        return <<<PROMPT
Brand Context:
- Brand Name: {$context['brand_name']}
- Industry: {$context['industry']}
- Brand Tone: {$context['brand_tone']}
- Target Audience: {$context['target_audience']}
- Business Goals: {$context['business_goals']}
- Do Not Say: {$context['forbidden_words']}{$budgetInfo}

Page Analytics:
- Total Followers: {$context['follower_count']}
- Average Engagement Rate: {$context['engagement_rate']}%
- Top Performing Post Types: {$context['top_post_types']}
- Peak Activity Times: {$context['peak_times']}

Task: {$context['task_description']}

IMPORTANT: Generate ALL content, recommendations, and text in {$languageName} language only.
{$budgetInstruction}

Provide strategic recommendations in JSON format with:
- content_calendar (array of posts with day, time, content_type, topic, objective)
- strategic_recommendations (array of actionable suggestions)
PROMPT;
    }
}
