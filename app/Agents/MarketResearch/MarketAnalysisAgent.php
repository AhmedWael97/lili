<?php

namespace App\Agents\MarketResearch;

use App\Models\ResearchRequest;
use App\Models\MarketAnalysis;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class MarketAnalysisAgent
{
    /**
     * Analyze the market for a research request
     */
    public function analyzeMarket(ResearchRequest $request): MarketAnalysis
    {
        Log::info('Starting market analysis', [
            'request_id' => $request->id,
            'business_idea' => $request->business_idea
        ]);

        // Gather data
        $competitors = $request->competitors()->with('socialMetrics', 'socialIntelligence')->get();
        
        // Generate comprehensive analysis using GPT-4
        $analysis = $this->generateMarketAnalysis($request, $competitors);

        // Save to database
        return $this->saveMarketAnalysis($request, $analysis);
    }

    /**
     * Generate comprehensive market analysis using GPT-4
     */
    private function generateMarketAnalysis(ResearchRequest $request, $competitors): array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($request, $competitors);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert market research analyst with 20 years of experience. Provide comprehensive, data-driven market analysis in JSON format.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content;
            
            // Parse JSON response
            $analysis = json_decode($content, true);
            
            if (!$analysis) {
                throw new \Exception('Invalid JSON response from GPT-4');
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::error('Failed to generate market analysis', [
                'error' => $e->getMessage(),
                'request_id' => $request->id
            ]);

            // Return fallback analysis
            return $this->getFallbackAnalysis($request, $competitors);
        }
    }

    /**
     * Build comprehensive analysis prompt
     */
    private function buildAnalysisPrompt(ResearchRequest $request, $competitors): string
    {
        $competitorSummary = $this->buildCompetitorSummary($competitors);
        $socialMediaSummary = $this->buildSocialMediaSummary($competitors);

        return "
# Market Analysis Request

Business Idea: {$request->business_idea}
Location: {$request->location}

## Competitors Found: {$competitors->count()}

{$competitorSummary}

## Social Media Overview:

{$socialMediaSummary}

---

Provide a comprehensive market analysis in JSON format:

{
  \"market_size_estimate\": \"$X.XM or descriptive estimate\",
  \"growth_rate\": 12.5,
  \"competition_level\": \"low|medium|high\",
  \"target_audience\": {
    \"primary\": \"description of primary audience\",
    \"secondary\": \"description of secondary audience\",
    \"demographics\": {
      \"age_range\": \"25-45\",
      \"income_level\": \"middle to upper-middle class\",
      \"interests\": [\"interest1\", \"interest2\"]
    }
  },
  \"trends\": [
    {
      \"trend\": \"trend description\",
      \"impact\": \"positive|negative|neutral\",
      \"relevance\": \"how it affects this business\"
    }
  ],
  \"opportunities\": [
    {
      \"opportunity\": \"specific opportunity\",
      \"potential\": \"high|medium|low\",
      \"reasoning\": \"why this is an opportunity\"
    }
  ],
  \"threats\": [
    {
      \"threat\": \"specific threat\",
      \"severity\": \"high|medium|low\",
      \"mitigation\": \"how to address it\"
    }
  ],
  \"barriers_to_entry\": [
    {
      \"barrier\": \"specific barrier\",
      \"severity\": \"high|medium|low\",
      \"description\": \"detailed explanation\"
    }
  ],
  \"recommended_strategy\": {
    \"positioning\": \"how to position in the market\",
    \"differentiation\": \"how to stand out from competitors\",
    \"pricing_strategy\": \"recommended pricing approach\",
    \"marketing_channels\": [\"channel1\", \"channel2\"],
    \"key_success_factors\": [\"factor1\", \"factor2\"]
  },
  \"competitive_advantages\": [\"advantage1\", \"advantage2\"],
  \"market_gaps\": [\"gap1\", \"gap2\"]
}

Be specific, data-driven, and actionable. Base insights on the competitor data provided.
";
    }

    /**
     * Build competitor summary for prompt
     */
    private function buildCompetitorSummary($competitors): string
    {
        $summary = '';
        $count = 1;

        foreach ($competitors->take(10) as $competitor) {
            $socialPresence = [];
            if ($competitor->facebook_handle) $socialPresence[] = 'Facebook';
            if ($competitor->instagram_handle) $socialPresence[] = 'Instagram';
            if ($competitor->twitter_handle) $socialPresence[] = 'Twitter';
            
            $socialStr = !empty($socialPresence) ? implode(', ', $socialPresence) : 'No social media';

            $summary .= "{$count}. {$competitor->business_name}\n";
            $summary .= "   Website: {$competitor->website}\n";
            $summary .= "   Social: {$socialStr}\n";
            $summary .= "   Relevance Score: {$competitor->relevance_score}/100\n\n";
            
            $count++;
        }

        return $summary;
    }

    /**
     * Build social media summary
     */
    private function buildSocialMediaSummary($competitors): string
    {
        $totalFollowers = 0;
        $platforms = ['facebook' => 0, 'instagram' => 0, 'twitter' => 0];
        $avgEngagement = [];

        foreach ($competitors as $competitor) {
            foreach ($competitor->socialMetrics as $metric) {
                $totalFollowers += $metric->followers ?? 0;
                $platforms[$metric->platform]++;
                
                if ($metric->avg_engagement_rate) {
                    $avgEngagement[] = $metric->avg_engagement_rate;
                }
            }
        }

        $avgEngagementRate = !empty($avgEngagement) ? round(array_sum($avgEngagement) / count($avgEngagement), 2) : 0;

        $summary = "Total combined followers across competitors: " . number_format($totalFollowers) . "\n";
        $summary .= "Platform presence:\n";
        $summary .= "  - Facebook: {$platforms['facebook']} competitors\n";
        $summary .= "  - Instagram: {$platforms['instagram']} competitors\n";
        $summary .= "  - Twitter: {$platforms['twitter']} competitors\n";
        $summary .= "Average engagement rate: {$avgEngagementRate}%\n";

        return $summary;
    }

    /**
     * Save market analysis to database
     */
    private function saveMarketAnalysis(ResearchRequest $request, array $analysis): MarketAnalysis
    {
        return MarketAnalysis::create([
            'research_request_id' => $request->id,
            'market_size_estimate' => $analysis['market_size_estimate'] ?? 'Unknown',
            'growth_rate' => $analysis['growth_rate'] ?? null,
            'competition_level' => $analysis['competition_level'] ?? 'medium',
            'target_audience' => $analysis['target_audience'] ?? [],
            'trends' => $analysis['trends'] ?? [],
            'opportunities' => $analysis['opportunities'] ?? [],
            'threats' => $analysis['threats'] ?? [],
            'barriers_to_entry' => $analysis['barriers_to_entry'] ?? [],
            'ai_analysis' => json_encode($analysis['recommended_strategy'] ?? []),
        ]);
    }

    /**
     * Get fallback analysis if GPT-4 fails
     */
    private function getFallbackAnalysis(ResearchRequest $request, $competitors): array
    {
        $competitorCount = $competitors->count();
        
        $competitionLevel = 'medium';
        if ($competitorCount < 5) $competitionLevel = 'low';
        if ($competitorCount > 15) $competitionLevel = 'high';

        return [
            'market_size_estimate' => 'Analysis in progress',
            'growth_rate' => null,
            'competition_level' => $competitionLevel,
            'target_audience' => [
                'primary' => 'Analysis based on location and business type',
                'secondary' => 'To be determined',
            ],
            'trends' => [
                ['trend' => 'Market research in progress', 'impact' => 'neutral']
            ],
            'opportunities' => [
                ['opportunity' => 'Found ' . $competitorCount . ' competitors to analyze', 'potential' => 'medium']
            ],
            'threats' => [
                ['threat' => 'Competitive market', 'severity' => $competitionLevel]
            ],
            'barriers_to_entry' => [],
            'recommended_strategy' => [
                'positioning' => 'Differentiate from competitors',
                'differentiation' => 'Focus on unique value proposition',
            ],
        ];
    }
}
