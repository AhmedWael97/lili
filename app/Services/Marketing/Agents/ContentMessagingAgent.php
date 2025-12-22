<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use Illuminate\Support\Facades\Log;

/**
 * Content & Messaging Agent (Strategy-Level)
 * Defines brand messaging and content themes
 */
class ContentMessagingAgent
{
    protected OpenAIService $openai;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Brand Messaging & Content Strategist AI**.

Your task is to:
- Define core brand messages
- Suggest content themes
- Propose ad angles and hooks

Rules:
- Do NOT generate final creatives
- Focus on strategy and direction
- Adapt tone to culture and country

Output in JSON format:
{
  "messaging_pillars": [
    {"pillar": "Pillar name", "message": "Core message", "why": "Rationale"}
  ],
  "content_themes": [
    {"theme": "Theme name", "description": "What to cover", "formats": ["blog", "video"]}
  ],
  "creative_angles": [
    {"angle": "Hook/angle", "target_emotion": "emotion", "use_case": "When to use"}
  ],
  "tone_guidelines": {
    "personality": "description",
    "do": ["guideline1"],
    "dont": ["guideline1"]
  }
}
PROMPT;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    public function generate(array $params): array
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
                'agent' => 'ContentMessagingAgent',
            ];

        } catch (\Exception $e) {
            Log::error('ContentMessagingAgent error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function buildUserMessage(array $params): string
    {
        $message = "Brand Information:\n";
        $message .= "Name: " . ($params['brand_name'] ?? 'N/A') . "\n";
        $message .= "Industry: " . ($params['industry'] ?? 'N/A') . "\n";
        $message .= "Country: " . ($params['country'] ?? 'N/A') . "\n";
        $message .= "Description: " . ($params['description'] ?? 'N/A') . "\n\n";

        if (!empty($params['positioning'])) {
            $message .= "Positioning:\n" . json_encode($params['positioning'], JSON_PRETTY_PRINT) . "\n\n";
        }

        if (!empty($params['target_audience'])) {
            $message .= "Target Audience: " . json_encode($params['target_audience']) . "\n\n";
        }

        $message .= "Create comprehensive messaging pillars, content themes, and creative angles.";

        return $message;
    }
}
