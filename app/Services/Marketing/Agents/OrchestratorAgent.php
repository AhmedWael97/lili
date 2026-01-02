<?php

namespace App\Services\Marketing\Agents;

use App\Models\Brand;
use App\Models\StrategyPlan;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrator Agent - Marketing OS Brain
 * Coordinates all specialized agents and produces cohesive strategy
 */
class OrchestratorAgent
{
    protected MarketResearchAgent $marketAgent;
    protected CompetitorIntelligenceAgent $competitorAgent;
    protected SWOTAgent $swotAgent;
    protected StrategyAgent $strategyAgent;
    protected ContentMessagingAgent $messagingAgent;
    protected AnalyticsBenchmarkAgent $analyticsAgent;
    protected ComplianceAgent $complianceAgent;

    public function __construct(
        MarketResearchAgent $marketAgent,
        CompetitorIntelligenceAgent $competitorAgent,
        SWOTAgent $swotAgent,
        StrategyAgent $strategyAgent,
        ContentMessagingAgent $messagingAgent,
        AnalyticsBenchmarkAgent $analyticsAgent,
        ComplianceAgent $complianceAgent
    ) {
        $this->marketAgent = $marketAgent;
        $this->competitorAgent = $competitorAgent;
        $this->swotAgent = $swotAgent;
        $this->strategyAgent = $strategyAgent;
        $this->messagingAgent = $messagingAgent;
        $this->analyticsAgent = $analyticsAgent;
        $this->complianceAgent = $complianceAgent;
    }

    /**
     * Generate complete marketing strategy by orchestrating all agents
     */
    public function generateStrategy(Brand $brand, array $options = []): array
    {
        try {
            Log::info("Orchestrator: Starting strategy generation for brand {$brand->id}");

            $results = [];
            
            // Prepare language instruction for all agents
            $languageInstruction = $brand->language === 'ar' 
                ? 'IMPORTANT: You MUST respond ENTIRELY in Arabic language. All analysis, recommendations, and content must be in Arabic (العربية).'
                : '';
            
            // Step 1: Market Research
            Log::info("Orchestrator: Running MarketResearchAgent");
            $marketResult = $this->marketAgent->analyze([
                'industry' => $brand->industry,
                'country' => $brand->country,
                'target_audience' => $brand->target_audience,
                'additional_context' => $brand->description,
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
            ]);

            if (!$marketResult['success']) {
                return $this->getErrorResponse('Market research failed: ' . $marketResult['error']);
            }

            $results['market_research'] = $marketResult['data'];

            // Step 2: Competitor Intelligence (if competitors exist)
            $competitors = $brand->competitors;
            $competitorData = [];

            if ($competitors->count() > 0) {
                Log::info("Orchestrator: Analyzing {$competitors->count()} competitors");
                
                foreach ($competitors as $competitor) {
                    if ($competitor->needsRefresh()) {
                        $compResult = $this->competitorAgent->analyze([
                            'name' => $competitor->name,
                            'website' => $competitor->website,
                            'industry' => $brand->industry,
                        ]);

                        if ($compResult['success']) {
                            $competitorData[] = $compResult['data'];
                        }
                    } else {
                        // Use cached data
                        $competitorData[] = [
                            'positioning' => $competitor->positioning,
                            'strengths' => $competitor->strengths,
                            'weaknesses' => $competitor->weaknesses,
                        ];
                    }
                }
            }

            $results['competitors'] = $competitorData;

            // Step 3: SWOT Analysis & Positioning
            Log::info("Orchestrator: Running SWOTAgent");
            $swotResult = $this->swotAgent->analyze([
                'industry' => $brand->industry,
                'country' => $brand->country,
                'description' => $brand->description,
                'market_data' => $results['market_research'],
                'competitor_data' => $competitorData,
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
            ]);

            if (!$swotResult['success']) {
                return $this->getErrorResponse('SWOT analysis failed: ' . $swotResult['error']);
            }

            $results['swot'] = $swotResult['data']['swot'];
            $results['positioning'] = $swotResult['data']['positioning'];

            // Step 4: Strategy & Budget Allocation
            Log::info("Orchestrator: Running StrategyAgent");
            $strategyResult = $this->strategyAgent->generate([
                'industry' => $brand->industry,
                'country' => $brand->country,
                'monthly_budget' => $brand->monthly_budget,
                'budget_tier' => $brand->budget_tier,
                'swot' => $results['swot'],
                'positioning' => $results['positioning'],
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
            ]);

            if (!$strategyResult['success']) {
                return $this->getErrorResponse('Strategy generation failed: ' . $strategyResult['error']);
            }

            $results['strategy'] = $strategyResult['data'];

            // Step 5: Content & Messaging
            Log::info("Orchestrator: Running ContentMessagingAgent");
            $messagingResult = $this->messagingAgent->generate([
                'brand_name' => $brand->name,
                'industry' => $brand->industry,
                'country' => $brand->country,
                'description' => $brand->description,
                'positioning' => $results['positioning'],
                'target_audience' => $brand->target_audience,
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
            ]);

            if (!$messagingResult['success']) {
                return $this->getErrorResponse('Messaging strategy failed: ' . $messagingResult['error']);
            }

            $results['messaging'] = $messagingResult['data'];

            // Step 6: Analytics & Benchmarks
            Log::info("Orchestrator: Running AnalyticsBenchmarkAgent");
            $channels = array_column($results['strategy']['channel_strategy']['primary_channels'] ?? [], 'channel');
            
            $analyticsResult = $this->analyticsAgent->generate([
                'industry' => $brand->industry,
                'country' => $brand->country,
                'channels' => $channels,
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
            ]);

            if ($analyticsResult['success']) {
                $results['analytics'] = $analyticsResult['data'];
            }

            // Step 7: Compliance & Risk Assessment
            Log::info("Orchestrator: Running ComplianceAgent");
            $complianceResult = $this->complianceAgent->assess([
                'industry' => $brand->industry,
                'country' => $brand->country,
                'products_services' => $brand->products_services,
                'language' => $brand->language,
                'language_instruction' => $languageInstruction,
                'strategy' => $results['strategy'],
            ]);

            if ($complianceResult['success']) {
                $results['compliance'] = $complianceResult['data'];
            }

            // Save strategy plan
            $strategyPlan = $this->saveStrategyPlan($brand, $results);

            Log::info("Orchestrator: Strategy generation completed successfully");

            return [
                'success' => true,
                'strategy_plan_id' => $strategyPlan->id,
                'data' => $results,
            ];

        } catch (\Exception $e) {
            Log::error('Orchestrator error: ' . $e->getMessage());
            return $this->getErrorResponse($e->getMessage());
        }
    }

    /**
     * Save complete strategy to database
     */
    protected function saveStrategyPlan(Brand $brand, array $results): StrategyPlan
    {
        return StrategyPlan::create([
            'brand_id' => $brand->id,
            'name' => 'Marketing Strategy ' . now()->format('M Y'),
            'status' => 'draft',
            'swot_analysis' => $results['swot'] ?? null,
            'positioning' => $results['positioning'] ?? null,
            'channel_strategy' => $results['strategy']['channel_strategy'] ?? null,
            'funnel_design' => $results['strategy']['funnel_design'] ?? null,
            'budget_allocation' => $results['strategy']['budget_allocation'] ?? null,
            'content_themes' => $results['messaging']['content_themes'] ?? null,
            'messaging_pillars' => $results['messaging']['messaging_pillars'] ?? null,
            'kpis' => $results['analytics']['kpis'] ?? null,
            'execution_priorities' => $results['strategy']['execution_priorities'] ?? null,
            'risks_compliance' => $results['compliance'] ?? null,
            'generated_at' => now(),
        ]);
    }

    protected function getErrorResponse(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
            'agent' => 'OrchestratorAgent',
        ];
    }
}
