<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use Illuminate\Support\Facades\Log;

/**
 * Strategy & Budget Allocation Agent
 * Designs go-to-market strategy with channel selection and budget allocation
 */
class StrategyAgent
{
    protected OpenAIService $openai;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Marketing Strategy Architect AI**.

Your task is to design a complete go-to-market strategy.

You must:
- Select optimal channels
- Define funnel stages
- Allocate budget proportionally
- Recommend content formats

Rules:
- Budget must adapt to scale
- Prefer fewer channels executed well
- Strategy must be executable by humans

Output in JSON format:
{
  "channel_strategy": {
    "primary_channels": [{"channel": "name", "priority": "high|medium|low", "rationale": "why"}],
    "secondary_channels": [{"channel": "name", "rationale": "why"}]
  },
  "funnel_design": {
    "awareness": {"tactics": ["tactic1"], "kpis": ["kpi1"]},
    "consideration": {"tactics": ["tactic1"], "kpis": ["kpi1"]},
    "conversion": {"tactics": ["tactic1"], "kpis": ["kpi1"]},
    "retention": {"tactics": ["tactic1"], "kpis": ["kpi1"]}
  },
  "budget_allocation": {
    "breakdown": [{"channel": "name", "percentage": 30, "amount": 150}],
    "rationale": "Explanation of allocation strategy"
  },
  "execution_priorities": [
    {"priority": 1, "action": "First action", "timeline": "Week 1-2"},
    {"priority": 2, "action": "Second action", "timeline": "Week 3-4"}
  ],
  "content_formats": ["format1", "format2"]
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

            $result = $this->openai->chatJson($this->systemPrompt, $userMessage, [
                'temperature' => 0.7,
            ]);

            if (!$result['success']) {
                return ['success' => false, 'error' => $result['error']];
            }

            return [
                'success' => true,
                'data' => $result['data'],
                'agent' => 'StrategyAgent',
            ];

        } catch (\Exception $e) {
            Log::error('StrategyAgent error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function buildUserMessage(array $params): string
    {
        $budget = $params['monthly_budget'] ?? 0;
        $budgetTier = $params['budget_tier'] ?? 'small';

        $message = "Business Information:\n";
        $message .= "Industry: " . ($params['industry'] ?? 'N/A') . "\n";
        $message .= "Country: " . ($params['country'] ?? 'N/A') . "\n";
        $message .= "Monthly Budget: $" . number_format($budget, 2) . " ({$budgetTier} tier)\n\n";

        if (!empty($params['swot'])) {
            $message .= "SWOT Analysis:\n" . json_encode($params['swot'], JSON_PRETTY_PRINT) . "\n\n";
        }

        if (!empty($params['positioning'])) {
            $message .= "Positioning:\n" . json_encode($params['positioning'], JSON_PRETTY_PRINT) . "\n\n";
        }

        $message .= "Design a complete marketing strategy with channel selection, funnel design, and budget allocation.";

        return $message;
    }
}
