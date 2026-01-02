<?php

namespace App\Agents\MarketResearch;

use App\Models\ResearchRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ReportGeneratorAgent
{
    /**
     * Generate comprehensive report for a research request
     */
    public function generateReport(ResearchRequest $request): Report
    {
        Log::info('Generating report', ['request_id' => $request->id]);

        // Load all data
        $request->load([
            'competitors.socialMetrics',
            'competitors.socialIntelligence',
            'marketAnalysis'
        ]);

        // Generate executive summary
        $executiveSummary = $this->generateExecutiveSummary($request);

        // Compile report data
        $reportData = $this->compileReportData($request);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($request);

        // Generate action plan
        $actionPlan = $this->generateActionPlan($request);

        // Save report
        return $this->saveReport($request, [
            'executive_summary' => $executiveSummary,
            'report_data' => $reportData,
            'recommendations' => $recommendations,
            'action_plan' => $actionPlan,
        ]);
    }

    /**
     * Generate executive summary using GPT-4
     */
    private function generateExecutiveSummary(ResearchRequest $request): string
    {
        try {
            $marketAnalysis = $request->marketAnalysis;
            $competitorCount = $request->competitors()->count();
            
            $prompt = "
Write a compelling executive summary (3-4 paragraphs) for this market research:

Business Idea: {$request->business_idea}
Location: {$request->location}
Competitors Found: {$competitorCount}
Competition Level: {$marketAnalysis->competition_level}
Market Size: {$marketAnalysis->market_size_estimate}
Growth Rate: {$marketAnalysis->growth_rate}%

Key Opportunities:
" . json_encode($marketAnalysis->opportunities, JSON_PRETTY_PRINT) . "

Key Threats:
" . json_encode($marketAnalysis->threats, JSON_PRETTY_PRINT) . "

Write in a professional but encouraging tone. Focus on:
1. The business opportunity
2. Key market findings
3. Main competitive advantages
4. Clear next steps

Make it actionable and motivating.
";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business consultant writing executive summaries for entrepreneurs.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            return $response->choices[0]->message->content;

        } catch (\Exception $e) {
            Log::error('Failed to generate executive summary', ['error' => $e->getMessage()]);
            
            return "This market research analyzes the opportunity for {$request->business_idea} in {$request->location}. We found {$competitorCount} relevant competitors and assessed the market conditions. The analysis includes competitive landscape, market opportunities, and strategic recommendations to help you successfully enter this market.";
        }
    }

    /**
     * Compile all report data into structured format
     */
    private function compileReportData(ResearchRequest $request): array
    {
        $competitors = $request->competitors()
            ->with('socialMetrics', 'socialIntelligence')
            ->orderByDesc('relevance_score')
            ->get();

        return [
            'overview' => [
                'business_idea' => $request->business_idea,
                'location' => $request->location,
                'analyzed_at' => now()->toDateTimeString(),
                'competitors_analyzed' => $competitors->count(),
            ],
            'competitors' => $competitors->map(function ($competitor) {
                $totalFollowers = $competitor->socialMetrics->sum('followers');
                $avgEngagement = $competitor->socialMetrics->avg('avg_engagement_rate');

                return [
                    'name' => $competitor->business_name,
                    'website' => $competitor->website,
                    'relevance_score' => $competitor->relevance_score,
                    'social_presence' => [
                        'facebook' => $competitor->facebook_handle,
                        'instagram' => $competitor->instagram_handle,
                        'twitter' => $competitor->twitter_handle,
                        'total_followers' => $totalFollowers,
                        'avg_engagement' => round($avgEngagement ?? 0, 2) . '%',
                    ],
                    'strengths' => $competitor->socialIntelligence->strengths ?? [],
                    'weaknesses' => $competitor->socialIntelligence->weaknesses ?? [],
                ];
            })->toArray(),
            'market_insights' => [
                'market_size' => $request->marketAnalysis->market_size_estimate ?? 'Unknown',
                'growth_rate' => $request->marketAnalysis->growth_rate ?? 0,
                'competition_level' => $request->marketAnalysis->competition_level ?? 'medium',
                'trends' => $request->marketAnalysis->trends ?? [],
                'opportunities' => $request->marketAnalysis->opportunities ?? [],
                'threats' => $request->marketAnalysis->threats ?? [],
            ],
        ];
    }

    /**
     * Generate strategic recommendations
     */
    private function generateRecommendations(ResearchRequest $request): array
    {
        try {
            $marketAnalysis = $request->marketAnalysis;
            $aiAnalysis = is_string($marketAnalysis->ai_analysis) 
                ? json_decode($marketAnalysis->ai_analysis, true) 
                : $marketAnalysis->ai_analysis;

            $prompt = "
Based on this market analysis:

Business: {$request->business_idea}
Location: {$request->location}
Competition: {$marketAnalysis->competition_level}
Market Gaps: " . json_encode($aiAnalysis['market_gaps'] ?? [], JSON_PRETTY_PRINT) . "
Opportunities: " . json_encode($marketAnalysis->opportunities, JSON_PRETTY_PRINT) . "

Generate 5-7 specific, actionable recommendations in JSON format:

{
  \"recommendations\": [
    {
      \"category\": \"positioning|pricing|marketing|product|operations\",
      \"title\": \"brief recommendation title\",
      \"description\": \"detailed explanation\",
      \"priority\": \"high|medium|low\",
      \"expected_impact\": \"what this will achieve\"
    }
  ]
}
";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business strategy consultant providing actionable recommendations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

            $content = $response->choices[0]->message->content;
            $parsed = json_decode($content, true);

            return $parsed['recommendations'] ?? $this->getFallbackRecommendations();

        } catch (\Exception $e) {
            Log::error('Failed to generate recommendations', ['error' => $e->getMessage()]);
            return $this->getFallbackRecommendations();
        }
    }

    /**
     * Generate 30-day action plan
     */
    private function generateActionPlan(ResearchRequest $request): array
    {
        try {
            $prompt = "
Create a detailed 30-day action plan for launching this business:

Business: {$request->business_idea}
Location: {$request->location}
Competition Level: {$request->marketAnalysis->competition_level}

Generate a JSON action plan with specific tasks:

{
  \"weeks\": [
    {
      \"week\": 1,
      \"focus\": \"Foundation\",
      \"tasks\": [
        {
          \"task\": \"specific actionable task\",
          \"description\": \"how to complete it\",
          \"resources_needed\": [\"resource1\", \"resource2\"],
          \"expected_outcome\": \"what this achieves\"
        }
      ]
    }
  ]
}

Create 4 weeks with 3-5 tasks each. Be specific and actionable.
";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business consultant creating launch plans for entrepreneurs.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            $content = $response->choices[0]->message->content;
            $parsed = json_decode($content, true);

            return $parsed['weeks'] ?? $this->getFallbackActionPlan();

        } catch (\Exception $e) {
            Log::error('Failed to generate action plan', ['error' => $e->getMessage()]);
            return $this->getFallbackActionPlan();
        }
    }

    /**
     * Save report to database
     */
    private function saveReport(ResearchRequest $request, array $data): Report
    {
        return Report::create([
            'research_request_id' => $request->id,
            'executive_summary' => $data['executive_summary'],
            'report_data' => $data['report_data'],
            'recommendations' => $data['recommendations'],
            'action_plan' => $data['action_plan'],
            'pdf_path' => null, // PDF generation can be added later
        ]);
    }

    /**
     * Fallback recommendations if GPT-4 fails
     */
    private function getFallbackRecommendations(): array
    {
        return [
            [
                'category' => 'positioning',
                'title' => 'Define your unique value proposition',
                'description' => 'Clearly articulate what makes your business different from competitors.',
                'priority' => 'high',
                'expected_impact' => 'Clear market differentiation'
            ],
            [
                'category' => 'marketing',
                'title' => 'Build strong social media presence',
                'description' => 'Focus on Instagram and Facebook based on competitor analysis.',
                'priority' => 'high',
                'expected_impact' => 'Increased brand awareness'
            ],
            [
                'category' => 'pricing',
                'title' => 'Competitive pricing strategy',
                'description' => 'Price competitively while maintaining quality perception.',
                'priority' => 'medium',
                'expected_impact' => 'Market penetration'
            ],
        ];
    }

    /**
     * Fallback action plan if GPT-4 fails
     */
    private function getFallbackActionPlan(): array
    {
        return [
            [
                'week' => 1,
                'focus' => 'Foundation',
                'tasks' => [
                    [
                        'task' => 'Register business and obtain permits',
                        'description' => 'Complete legal registration and licensing',
                        'resources_needed' => ['Legal consultation', 'Business license fees'],
                        'expected_outcome' => 'Legal entity established'
                    ],
                    [
                        'task' => 'Create social media accounts',
                        'description' => 'Set up Facebook, Instagram business pages',
                        'resources_needed' => ['Business email', 'Brand assets'],
                        'expected_outcome' => 'Online presence established'
                    ]
                ]
            ],
            [
                'week' => 2,
                'focus' => 'Product Development',
                'tasks' => [
                    [
                        'task' => 'Finalize product/service offerings',
                        'description' => 'Define your core offerings based on market gaps',
                        'resources_needed' => ['Market research', 'Testing budget'],
                        'expected_outcome' => 'Product-market fit validated'
                    ]
                ]
            ],
        ];
    }
}
