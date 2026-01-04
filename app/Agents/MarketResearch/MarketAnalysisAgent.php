<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\MarketData;
use App\Models\ResearchRequest;

class MarketAnalysisAgent extends BaseAgent
{
    protected string $name = 'MarketAnalysisAgent';
    protected string $description = 'Analyzes market size, trends, and competitive landscape using GPT-4';

    /**
     * Analyze market for a research request
     *
     * @param ResearchRequest $request
     * @param array $competitors
     * @return MarketData
     */
    public function execute(...$params): MarketData
    {
        /** @var ResearchRequest $request */
        $request = $params[0];
        /** @var array $competitors */
        $competitors = $params[1] ?? [];

        $this->log("Analyzing market for {$request->business_idea}");

        // Build comprehensive analysis prompt
        $analysis = $this->analyzeMarket($request, $competitors);

        // Create market data record
        $marketData = MarketData::create([
            'research_request_id' => $request->id,
            'market_size_estimate' => $analysis['market_size_estimate'] ?? 'Unknown',
            'growth_rate' => $analysis['growth_rate'] ?? null,
            'market_maturity' => $analysis['market_maturity'] ?? 'growing',
            'competition_level' => $this->determineCompetitionLevel(count($competitors)),
            'target_audience' => $analysis['target_audience'] ?? [],
            'trends' => $analysis['trends'] ?? [],
            'technology_trends' => $analysis['technology_trends'] ?? [],
            'barriers_to_entry' => $analysis['barriers_to_entry'] ?? [],
            'market_overview' => $analysis['market_overview'] ?? '',
        ]);

        $this->log("Market analysis completed");

        return $marketData;
    }

    /**
     * Analyze market using GPT-4
     *
     * @param ResearchRequest $request
     * @param array $competitors
     * @return array
     */
    private function analyzeMarket(ResearchRequest $request, array $competitors): array
    {
        $competitorNames = array_map(fn($c) => $c->name, $competitors);
        $competitorList = implode(', ', array_slice($competitorNames, 0, 10));

        $prompt = "
Analyze the market for this business idea:

Business Idea: {$request->business_idea}
Location: {$request->location}
Competitors Found: {$competitorList}
Number of Competitors: " . count($competitors) . "

Provide a comprehensive market analysis including:

1. Market Size Estimate (e.g., \"\$500M - \$1B annually in {$request->location}\")
2. Growth Rate (annual percentage, e.g., 12.5)
3. Market Maturity (emerging, growing, mature, or declining)
4. Target Audience (detailed customer segments)
5. Current Market Trends (3-5 key trends)
6. Technology Trends affecting this market
7. Barriers to Entry (capital, regulatory, technology, etc.)
8. Market Overview (2-3 paragraph summary)

Return JSON:
{
  \"market_size_estimate\": \"Estimated market size\",
  \"growth_rate\": 12.5,
  \"market_maturity\": \"growing\",
  \"target_audience\": [
    {
      \"segment\": \"Segment name\",
      \"description\": \"Who they are\",
      \"size\": \"Approximate size\"
    }
  ],
  \"trends\": [
    {
      \"trend\": \"Trend name\",
      \"description\": \"What's happening\",
      \"impact\": \"How it affects the market\"
    }
  ],
  \"technology_trends\": [
    {
      \"technology\": \"Tech name\",
      \"adoption\": \"High/Medium/Low\",
      \"impact\": \"Description\"
    }
  ],
  \"barriers_to_entry\": [
    {
      \"barrier\": \"Barrier name\",
      \"severity\": \"High/Medium/Low\",
      \"description\": \"Details\"
    }
  ],
  \"market_overview\": \"Comprehensive 2-3 paragraph overview of the market\"
}

Be specific and realistic. Use your knowledge of the industry.
";

        $response = $this->callGPTJson($prompt, 'gpt-4o', 3000);

        return $response ?? [];
    }

    /**
     * Determine competition level based on number of competitors
     *
     * @param int $competitorCount
     * @return string
     */
    private function determineCompetitionLevel(int $competitorCount): string
    {
        if ($competitorCount <= 3) {
            return 'low';
        } elseif ($competitorCount <= 7) {
            return 'medium';
        } else {
            return 'high';
        }
    }
}
