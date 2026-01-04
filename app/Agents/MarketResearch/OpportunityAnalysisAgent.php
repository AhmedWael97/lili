<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\ResearchRequest;

class OpportunityAnalysisAgent extends BaseAgent
{
    protected string $name = 'OpportunityAnalysisAgent';
    protected string $description = 'Identifies market opportunities, gaps, and risks';

    /**
     * Analyze opportunities and risks
     *
     * @param ResearchRequest $request
     * @return array
     */
    public function execute(...$params): array
    {
        /** @var ResearchRequest $request */
        $request = $params[0];

        $this->log("Analyzing opportunities and risks for {$request->business_idea}");

        // Gather all collected data
        $marketData = $request->marketData;
        $customerInsights = $request->customerInsights;
        $competitors = $request->competitors;

        // Analyze with GPT-4
        $analysis = $this->analyzeOpportunitiesAndRisks(
            $request,
            $marketData,
            $customerInsights,
            $competitors
        );

        $this->log("Opportunity analysis completed");

        return $analysis;
    }

    /**
     * Analyze opportunities and risks with GPT-4
     *
     * @param ResearchRequest $request
     * @param $marketData
     * @param $customerInsights
     * @param $competitors
     * @return array
     */
    private function analyzeOpportunitiesAndRisks($request, $marketData, $customerInsights, $competitors): array
    {
        // Prepare data summaries
        $marketSummary = $this->summarizeMarketData($marketData);
        $customerSummary = $this->summarizeCustomerInsights($customerInsights);
        $competitorSummary = $this->summarizeCompetitors($competitors);

        $prompt = "
Analyze market opportunities, gaps, and risks for this business:

Business Idea: {$request->business_idea}
Location: {$request->location}

Market Data:
{$marketSummary}

Customer Insights:
{$customerSummary}

Competitors:
{$competitorSummary}

Provide comprehensive analysis of:

1. Market Opportunities (where can this business succeed?)
2. Competitive Gaps (what are competitors missing?)
3. Customer Segments Not Served (underserved groups)
4. Feature Gaps (functionality competitors lack)
5. Pricing Gaps (pricing opportunities)
6. Market Risks (threats to success)
7. Barriers to Entry (challenges to overcome)
8. Competitive Threats (existing player advantages)
9. Strategic Recommendations (how to position and compete)

Return JSON:
{
  \"opportunities\": [
    {
      \"opportunity\": \"Opportunity name\",
      \"description\": \"Details\",
      \"potential\": \"High/Medium/Low\",
      \"rationale\": \"Why this is an opportunity\"
    }
  ],
  \"gaps\": {
    \"competitive_gaps\": [
      {
        \"gap\": \"What's missing\",
        \"description\": \"Details\",
        \"exploitability\": \"High/Medium/Low\"
      }
    ],
    \"underserved_segments\": [
      {
        \"segment\": \"Customer group\",
        \"reason\": \"Why they're underserved\",
        \"size\": \"Market size estimate\"
      }
    ],
    \"feature_gaps\": [
      {
        \"feature\": \"Missing feature\",
        \"demand\": \"High/Medium/Low\",
        \"competitive_advantage\": \"How it helps\"
      }
    ],
    \"pricing_gaps\": [
      {
        \"gap\": \"Pricing opportunity\",
        \"description\": \"Details\",
        \"potential_revenue_impact\": \"Estimate\"
      }
    ]
  },
  \"risks\": [
    {
      \"risk\": \"Risk name\",
      \"severity\": \"High/Medium/Low\",
      \"probability\": \"High/Medium/Low\",
      \"mitigation\": \"How to address it\"
    }
  ],
  \"barriers\": [
    {
      \"barrier\": \"Barrier name\",
      \"type\": \"Capital/Regulatory/Technology/Market\",
      \"severity\": \"High/Medium/Low\",
      \"overcome_strategy\": \"How to overcome\"
    }
  ],
  \"competitive_threats\": [
    {
      \"threat\": \"Threat description\",
      \"source\": \"Which competitor\",
      \"impact\": \"How it affects you\",
      \"counter_strategy\": \"How to respond\"
    }
  ],
  \"recommendations\": [
    {
      \"category\": \"Positioning/Pricing/Product/Marketing\",
      \"recommendation\": \"What to do\",
      \"priority\": \"High/Medium/Low\",
      \"rationale\": \"Why this matters\",
      \"implementation\": \"How to execute\"
    }
  ]
}

Be specific and actionable. Focus on realistic, achievable opportunities.
";

        $response = $this->callGPTJson($prompt, 'gpt-4o', 4000);

        return $response ?? [
            'opportunities' => [],
            'gaps' => [],
            'risks' => [],
            'barriers' => [],
            'competitive_threats' => [],
            'recommendations' => [],
        ];
    }

    /**
     * Summarize market data
     *
     * @param $marketData
     * @return string
     */
    private function summarizeMarketData($marketData): string
    {
        if (!$marketData) {
            return "No market data available.";
        }

        return "
Market Size: {$marketData->market_size_estimate}
Growth Rate: {$marketData->growth_rate}%
Maturity: {$marketData->market_maturity}
Competition Level: {$marketData->competition_level}
Trends: " . json_encode($marketData->trends) . "
Barriers: " . json_encode($marketData->barriers_to_entry);
    }

    /**
     * Summarize customer insights
     *
     * @param $customerInsights
     * @return string
     */
    private function summarizeCustomerInsights($customerInsights): string
    {
        if (!$customerInsights) {
            return "No customer insights available.";
        }

        return "
Personas: " . count($customerInsights->customer_personas ?? []) . " identified
Pain Points: " . json_encode($customerInsights->pain_points) . "
Needs: " . json_encode($customerInsights->needs) . "
Feature Requests: " . json_encode($customerInsights->feature_requests) . "
Common Complaints: " . json_encode($customerInsights->common_complaints);
    }

    /**
     * Summarize competitors
     *
     * @param $competitors
     * @return string
     */
    private function summarizeCompetitors($competitors): string
    {
        if (!$competitors || $competitors->count() === 0) {
            return "No competitors found.";
        }

        $summary = "Total Competitors: " . $competitors->count() . "\n";

        foreach ($competitors->take(5) as $competitor) {
            $summary .= "- {$competitor->name}: " . ($competitor->description ?? 'N/A') . "\n";
            $summary .= "  Rating: {$competitor->overall_rating}/5, Reviews: {$competitor->review_count}\n";
        }

        return $summary;
    }
}
