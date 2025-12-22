<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use Illuminate\Support\Facades\Log;

/**
 * Market Research Agent
 * Analyzes markets for given industry, country, and target audience
 */
class MarketResearchAgent
{
    protected OpenAIService $openai;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Market Research Analyst AI**.

Your task is to analyze a market for a given industry, country, and target audience.

You must:
- Estimate demand using trends and search behavior
- Identify market maturity level
- Highlight emerging opportunities
- Detect seasonality and risks

Inputs:
- Industry
- Country
- Target customer (if provided)

Rules:
- Use trend-based reasoning
- Avoid absolute numbers unless confident
- Always contextualize insights geographically

Output in JSON format:
{
  "market_overview": "Brief overview of the market",
  "maturity_level": "emerging|growing|mature|declining",
  "key_trends": ["trend1", "trend2"],
  "opportunities": ["opportunity1", "opportunity2"],
  "risks": ["risk1", "risk2"],
  "seasonality": {"high_season": "Q4", "low_season": "Q1", "notes": "details"},
  "target_audience_insights": "Insights about target audience",
  "estimated_market_size": "Small|Medium|Large"
}
PROMPT;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    /**
     * Analyze market for given parameters
     */
    public function analyze(array $params): array
    {
        try {
            $industry = $params['industry'] ?? 'general';
            $country = $params['country'] ?? 'US';
            $targetAudience = $params['target_audience'] ?? null;
            $additionalContext = $params['additional_context'] ?? '';

            $userMessage = $this->buildUserMessage($industry, $country, $targetAudience, $additionalContext);

            $result = $this->openai->chatJson($this->systemPrompt, $userMessage, [
                'temperature' => 0.6,
            ]);

            if (!$result['success']) {
                return $this->getErrorResponse($result['error']);
            }

            return [
                'success' => true,
                'data' => $result['data'],
                'agent' => 'MarketResearchAgent',
            ];

        } catch (\Exception $e) {
            Log::error('MarketResearchAgent error: ' . $e->getMessage());
            return $this->getErrorResponse($e->getMessage());
        }
    }

    /**
     * Build user message from parameters
     */
    protected function buildUserMessage(string $industry, string $country, ?string $targetAudience, string $additionalContext): string
    {
        $message = "Industry: {$industry}\n";
        $message .= "Country: {$country}\n";
        
        if ($targetAudience) {
            $message .= "Target Audience: {$targetAudience}\n";
        }

        if ($additionalContext) {
            $message .= "\nAdditional Context:\n{$additionalContext}\n";
        }

        $message .= "\nProvide a comprehensive market analysis for this industry and country.";

        return $message;
    }

    /**
     * Get error response
     */
    protected function getErrorResponse(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
            'agent' => 'MarketResearchAgent',
        ];
    }
}
