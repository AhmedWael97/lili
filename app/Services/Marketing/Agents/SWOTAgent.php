<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use Illuminate\Support\Facades\Log;

/**
 * SWOT & Positioning Agent
 * Generates SWOT analysis and positioning strategy
 */
class SWOTAgent
{
    protected OpenAIService $openai;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Strategic Marketing Consultant AI**.

Your task is to:
- Build a SWOT analysis based on market and competitor inputs
- Define a clear positioning strategy
- Recommend differentiation angles

Rules:
- SWOT must be evidence-based
- Avoid generic statements
- Tie positioning directly to market gaps

Output in JSON format:
{
  "swot": {
    "strengths": ["strength1", "strength2"],
    "weaknesses": ["weakness1", "weakness2"],
    "opportunities": ["opp1", "opp2"],
    "threats": ["threat1", "threat2"]
  },
  "positioning": {
    "statement": "One clear positioning statement",
    "differentiation_angles": ["angle1", "angle2"],
    "target_segment": "Primary target description"
  },
  "strategic_focus": ["focus_area1", "focus_area2"],
  "key_insights": ["insight1", "insight2"]
}
PROMPT;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    public function analyze(array $params): array
    {
        try {
            $userMessage = $this->buildUserMessage($params);

            $result = $this->openai->chatJson($this->systemPrompt, $userMessage);

            if (!$result['success']) {
                return ['success' => false, 'error' => $result['error']];
            }

            return [
                'success' => true,
                'data' => $result['data'],
                'agent' => 'SWOTAgent',
            ];

        } catch (\Exception $e) {
            Log::error('SWOTAgent error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function buildUserMessage(array $params): string
    {
        $message = "Business Context:\n";
        $message .= "Industry: " . ($params['industry'] ?? 'N/A') . "\n";
        $message .= "Country: " . ($params['country'] ?? 'N/A') . "\n";
        $message .= "Description: " . ($params['description'] ?? 'N/A') . "\n\n";

        if (!empty($params['market_data'])) {
            $message .= "Market Analysis:\n" . json_encode($params['market_data'], JSON_PRETTY_PRINT) . "\n\n";
        }

        if (!empty($params['competitor_data'])) {
            $message .= "Competitor Data:\n" . json_encode($params['competitor_data'], JSON_PRETTY_PRINT) . "\n\n";
        }

        $message .= "Generate a comprehensive SWOT analysis and positioning strategy.";

        return $message;
    }
}
