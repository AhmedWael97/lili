<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use Illuminate\Support\Facades\Log;

/**
 * Compliance & Risk Agent
 * Identifies legal, cultural, and platform risks
 */
class ComplianceAgent
{
    protected OpenAIService $openai;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Marketing Compliance & Risk AI**.

Your task is to:
- Identify legal, cultural, and platform risks
- Flag restricted industries
- Recommend safe alternatives

Rules:
- Be conservative
- Prefer compliance over growth

Output in JSON format:
{
  "risk_assessment": {
    "level": "low|medium|high",
    "summary": "Brief assessment"
  },
  "risks": [
    {"type": "legal|cultural|platform", "risk": "Description", "severity": "low|medium|high"}
  ],
  "mitigation_actions": [
    {"action": "What to do", "priority": "high|medium|low"}
  ],
  "platform_restrictions": ["restriction1"],
  "safe_practices": ["practice1"]
}
PROMPT;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    public function assess(array $params): array
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
                'agent' => 'ComplianceAgent',
            ];

        } catch (\Exception $e) {
            Log::error('ComplianceAgent error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function buildUserMessage(array $params): string
    {
        $message = "Business Information:\n";
        $message .= "Industry: " . ($params['industry'] ?? 'N/A') . "\n";
        $message .= "Country: " . ($params['country'] ?? 'N/A') . "\n";
        $message .= "Products/Services: " . json_encode($params['products_services'] ?? []) . "\n\n";

        if (!empty($params['strategy'])) {
            $message .= "Planned Strategy:\n" . json_encode($params['strategy'], JSON_PRETTY_PRINT) . "\n\n";
        }

        $message .= "Assess compliance risks and provide mitigation recommendations.";

        return $message;
    }
}
