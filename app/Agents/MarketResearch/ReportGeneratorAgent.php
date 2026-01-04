<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\Report;
use App\Models\ResearchRequest;

class ReportGeneratorAgent extends BaseAgent
{
    protected string $name = 'ReportGeneratorAgent';
    protected string $description = 'Compiles all data into comprehensive 12-section market research report';

    /**
     * Generate comprehensive report
     *
     * @param ResearchRequest $request
     * @param array $opportunityAnalysis
     * @return Report
     */
    public function execute(...$params): Report
    {
        /** @var ResearchRequest $request */
        $request = $params[0];
        /** @var array $opportunityAnalysis */
        $opportunityAnalysis = $params[1] ?? [];

        $this->log("Generating report for {$request->business_idea}");

        // Gather all data
        $reportData = $this->compileReportData($request, $opportunityAnalysis);

        // Generate executive summary
        $executiveSummary = $this->generateExecutiveSummary($request, $reportData);

        // Generate action plan
        $actionPlan = $this->generateActionPlan($request, $reportData);

        // Create report
        $report = Report::create([
            'research_request_id' => $request->id,
            'executive_summary' => $executiveSummary,
            'report_sections' => $reportData,
            'opportunities' => $opportunityAnalysis['opportunities'] ?? [],
            'risks' => $opportunityAnalysis['risks'] ?? [],
            'recommendations' => $opportunityAnalysis['recommendations'] ?? [],
            'action_plan' => $actionPlan,
            'competitor_count' => $request->competitors->count(),
            'review_count' => $this->getTotalReviewCount($request),
        ]);

        $this->log("Report generated successfully");

        return $report;
    }

    /**
     * Compile all report sections
     *
     * @param ResearchRequest $request
     * @param array $opportunityAnalysis
     * @return array
     */
    private function compileReportData(ResearchRequest $request, array $opportunityAnalysis): array
    {
        $marketData = $request->marketData;
        $customerInsights = $request->customerInsights;
        $competitors = $request->competitors;

        return [
            // Section 1: Market Problem & Customer Pain
            'market_problem' => [
                'problems_identified' => $customerInsights->pain_points ?? [],
                'affected_customers' => $customerInsights->customer_personas ?? [],
                'frequency' => 'Based on ' . $this->getTotalReviewCount($request) . ' reviews analyzed',
                'severity' => $this->assessPainSeverity($customerInsights),
                'current_workarounds' => $this->extractWorkarounds($customerInsights),
            ],

            // Section 2: Target Customer Profile
            'customer_profile' => [
                'personas' => $customerInsights->customer_personas ?? [],
                'demographics' => $this->extractDemographics($customerInsights),
                'budget_range' => $this->extractBudgetRange($competitors),
                'geographic_market' => $request->location,
            ],

            // Section 3: Market Demand
            'market_demand' => [
                'potential_customers' => $marketData->target_audience ?? [],
                'market_size' => $marketData->market_size_estimate ?? 'Unknown',
                'growth_trend' => $marketData->growth_rate ?? null,
                'existing_payment_behavior' => $this->analyzePricingWillingness($competitors),
            ],

            // Section 4: Competitor Landscape
            'competitors' => [
                'direct_competitors' => $this->formatCompetitors($competitors, 'direct'),
                'indirect_alternatives' => $this->formatCompetitors($competitors, 'indirect'),
                'market_positioning' => $this->analyzePositioning($competitors),
                'total_found' => $competitors->count(),
            ],

            // Section 5: Pricing Reality
            'pricing' => [
                'price_ranges' => $this->analyzePriceRanges($competitors),
                'pricing_models' => $this->analyzePricingModels($competitors),
                'willingness_to_pay' => $this->analyzeWillingnessToPay($customerInsights),
                'average_price' => $this->calculateAveragePrice($competitors),
            ],

            // Section 6: Customer Sentiment
            'sentiment' => [
                'common_complaints' => $customerInsights->common_complaints ?? [],
                'feature_requests' => $customerInsights->feature_requests ?? [],
                'satisfaction_drivers' => $customerInsights->satisfaction_drivers ?? [],
                'overall_sentiment' => $customerInsights->sentiment_summary ?? '',
            ],

            // Section 7: Market Size & Growth
            'market_analysis' => [
                'market_size' => $marketData->market_size_estimate ?? 'Unknown',
                'growth_rate' => $marketData->growth_rate ?? null,
                'market_maturity' => $marketData->market_maturity ?? 'growing',
                'trends' => $marketData->trends ?? [],
            ],

            // Section 8: Purchase Decision Process
            'purchase_process' => $customerInsights->purchase_decision_process ?? [
                'discovery' => 'Not enough data',
                'evaluation' => 'Not enough data',
                'decision_makers' => 'Not enough data',
                'timeframe' => 'Not enough data',
            ],

            // Section 9: Marketing Channels
            'marketing_channels' => $customerInsights->marketing_channels ?? [],

            // Section 10: Market Trends & Timing
            'trends' => [
                'market_trends' => $marketData->trends ?? [],
                'technology_trends' => $marketData->technology_trends ?? [],
                'regulatory_impacts' => $marketData->barriers_to_entry ?? [],
            ],

            // Section 11: Risks & Barriers
            'risks_barriers' => [
                'risks' => $opportunityAnalysis['risks'] ?? [],
                'barriers' => $opportunityAnalysis['barriers'] ?? [],
                'competitive_threats' => $opportunityAnalysis['competitive_threats'] ?? [],
            ],

            // Section 12: Opportunities & Gaps
            'opportunities' => [
                'market_opportunities' => $opportunityAnalysis['opportunities'] ?? [],
                'gaps' => $opportunityAnalysis['gaps'] ?? [],
                'competitive_advantage_areas' => $this->identifyAdvantageAreas($opportunityAnalysis),
            ],
        ];
    }

    /**
     * Generate executive summary
     *
     * @param ResearchRequest $request
     * @param array $reportData
     * @return string
     */
    private function generateExecutiveSummary(ResearchRequest $request, array $reportData): string
    {
        $prompt = "
Generate a compelling executive summary for this market research report:

Business Idea: {$request->business_idea}
Location: {$request->location}

Key Findings:
- Competitors Found: {$reportData['competitors']['total_found']}
- Market Size: {$reportData['market_analysis']['market_size']}
- Growth Rate: {$reportData['market_analysis']['growth_rate']}%
- Competition Level: " . ($request->marketData->competition_level ?? 'medium') . "
- Customer Pain Points: " . count($reportData['market_problem']['problems_identified']) . "
- Opportunities Identified: " . count($reportData['opportunities']['market_opportunities']) . "

Write a 3-4 paragraph executive summary that:
1. States the market opportunity clearly
2. Highlights key findings about customers and competition
3. Identifies the biggest opportunity
4. Provides a clear recommendation

Be concise, professional, and actionable.
";

        return $this->callGPT($prompt, 'gpt-4o', 1000) ?? 'Executive summary generation failed.';
    }

    /**
     * Generate 30-day action plan
     *
     * @param ResearchRequest $request
     * @param array $reportData
     * @return array
     */
    private function generateActionPlan(ResearchRequest $request, array $reportData): array
    {
        $prompt = "
Create a detailed 30-day action plan for launching this business:

Business Idea: {$request->business_idea}
Location: {$request->location}

Key Insights:
- Top Customer Pain Points: " . json_encode(array_slice($reportData['market_problem']['problems_identified'] ?? [], 0, 3)) . "
- Main Competitors: {$reportData['competitors']['total_found']}
- Market Opportunity: " . json_encode(array_slice($reportData['opportunities']['market_opportunities'] ?? [], 0, 2)) . "

Create a 4-week action plan with specific tasks for:
- Week 1: Foundation (business setup, research)
- Week 2: Product/Service Development
- Week 3: Marketing & Positioning
- Week 4: Launch Preparation

Return JSON:
{
  \"week_1\": {
    \"title\": \"Foundation\",
    \"tasks\": [
      {
        \"task\": \"Task description\",
        \"priority\": \"High/Medium/Low\",
        \"estimated_time\": \"X hours\",
        \"resources_needed\": \"What you need\"
      }
    ]
  },
  \"week_2\": {...},
  \"week_3\": {...},
  \"week_4\": {...}
}

Be specific and actionable.
";

        $response = $this->callGPTJson($prompt, 'gpt-4o', 2000);

        return $response ?? [];
    }

    // Helper methods...

    private function getTotalReviewCount(ResearchRequest $request): int
    {
        return $request->competitors->sum('review_count');
    }

    private function assessPainSeverity($customerInsights): string
    {
        $painPoints = $customerInsights->pain_points ?? [];
        $count = count($painPoints);

        if ($count >= 10) return 'High';
        if ($count >= 5) return 'Medium';
        return 'Low';
    }

    private function extractWorkarounds($customerInsights): array
    {
        // Would extract from review analysis
        return [];
    }

    private function extractDemographics($customerInsights): array
    {
        $personas = $customerInsights->customer_personas ?? [];
        return array_map(fn($p) => $p['demographics'] ?? 'Unknown', $personas);
    }

    private function extractBudgetRange($competitors): string
    {
        $prices = [];
        foreach ($competitors as $competitor) {
            foreach ($competitor->pricing as $pricing) {
                if ($pricing->price) {
                    $prices[] = $pricing->price;
                }
            }
        }

        if (empty($prices)) return 'Unknown';

        return '$' . min($prices) . ' - $' . max($prices);
    }

    private function analyzePricingWillingness($competitors): string
    {
        return $competitors->count() . ' competitors with active customers indicates proven willingness to pay';
    }

    private function formatCompetitors($competitors, $type): array
    {
        return $competitors->take(10)->map(function($c) {
            return [
                'name' => $c->name,
                'website' => $c->website,
                'rating' => $c->overall_rating,
                'reviews' => $c->review_count,
            ];
        })->toArray();
    }

    private function analyzePositioning($competitors): array
    {
        return [];
    }

    private function analyzePriceRanges($competitors): array
    {
        $ranges = [];
        foreach ($competitors as $competitor) {
            foreach ($competitor->pricing as $pricing) {
                $ranges[] = [
                    'tier' => $pricing->tier_name,
                    'price' => $pricing->price,
                    'period' => $pricing->billing_period,
                ];
            }
        }
        return $ranges;
    }

    private function analyzePricingModels($competitors): array
    {
        $models = [];
        foreach ($competitors as $competitor) {
            foreach ($competitor->pricing as $pricing) {
                $models[] = $pricing->pricing_model;
            }
        }
        return array_unique($models);
    }

    private function analyzeWillingnessToPay($customerInsights): string
    {
        return 'Based on customer feedback analysis';
    }

    private function calculateAveragePrice($competitors): ?float
    {
        $prices = [];
        foreach ($competitors as $competitor) {
            foreach ($competitor->pricing as $pricing) {
                if ($pricing->price) {
                    $prices[] = $pricing->price;
                }
            }
        }

        return empty($prices) ? null : array_sum($prices) / count($prices);
    }

    private function identifyAdvantageAreas($opportunityAnalysis): array
    {
        $gaps = $opportunityAnalysis['gaps'] ?? [];
        return array_merge(
            $gaps['competitive_gaps'] ?? [],
            $gaps['feature_gaps'] ?? []
        );
    }
}
