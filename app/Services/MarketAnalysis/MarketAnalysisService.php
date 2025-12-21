<?php

namespace App\Services\MarketAnalysis;

use App\Models\MarketInsight;
use App\Services\AI\OpenAIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketAnalysisService
{
    protected $openai;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
    }

    /**
     * Generate comprehensive SWOT analysis
     */
    public function generateSWOTAnalysis(int $userId, string $industry, array $businessContext, array $competitorData = []): array
    {
        try {
            // Check cache
            $cached = MarketInsight::where('user_id', $userId)
                ->where('industry', $industry)
                ->where('insight_type', 'swot')
                ->valid()
                ->first();
            
            if ($cached && !$cached->isExpired()) {
                return [
                    'success' => true,
                    'data' => $cached->ai_analysis,
                    'cached' => true,
                ];
            }
            
            // Gather market data
            $industryTrends = $this->getIndustryTrends($industry);
            $benchmarks = $this->getIndustryBenchmarks($industry);
            
            // Build context for AI
            $prompt = $this->buildSWOTPrompt($businessContext, $competitorData, $industryTrends, $benchmarks);
            
            // Generate AI analysis using GPT-4o-mini (cost-effective)
            $swotData = $this->openai->generateJSON(
                $prompt,
                'You are an expert marketing strategist and business analyst. Provide detailed, actionable SWOT analysis in JSON format.',
                'gpt-4o-mini'
            );
            
            // Cache for 7 days
            $insight = MarketInsight::create([
                'user_id' => $userId,
                'industry' => $industry,
                'insight_type' => 'swot',
                'data' => [
                    'business_context' => $businessContext,
                    'competitors' => $competitorData,
                ],
                'ai_analysis' => $swotData,
                'expires_at' => now()->addDays(7),
            ]);
            
            return [
                'success' => true,
                'data' => $swotData,
                'cached' => false,
            ];
            
        } catch (\Exception $e) {
            Log::error('SWOT analysis failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build SWOT analysis prompt
     */
    protected function buildSWOTPrompt(array $businessContext, array $competitorData, array $trends, array $benchmarks): string
    {
        $prompt = "Generate a comprehensive SWOT analysis for this business:\n\n";
        $prompt .= "Business Profile:\n";
        $prompt .= "- Industry: " . ($businessContext['industry'] ?? 'General') . "\n";
        $prompt .= "- Target Audience: " . ($businessContext['target_audience'] ?? 'N/A') . "\n";
        $prompt .= "- Products/Services: " . ($businessContext['products_services'] ?? 'N/A') . "\n";
        $prompt .= "- Unique Value: " . ($businessContext['unique_value_proposition'] ?? 'N/A') . "\n\n";
        
        if (!empty($competitorData)) {
            $prompt .= "Competitor Analysis:\n";
            foreach ($competitorData as $comp) {
                $prompt .= "- {$comp['name']}: {$comp['followers']} followers, {$comp['engagement_rate']}% engagement\n";
            }
            $prompt .= "\n";
        }
        
        if (!empty($trends)) {
            $prompt .= "Industry Trends:\n";
            foreach ($trends as $trend) {
                $prompt .= "- $trend\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "Provide analysis in this JSON format:\n";
        $prompt .= '{"strengths": ["strength1", "strength2", ...], "weaknesses": ["weakness1", ...], "opportunities": ["opportunity1", ...], "threats": ["threat1", ...], "key_insights": ["insight1", ...], "action_items": ["action1", ...]}';
        
        return $prompt;
    }

    /**
     * Get industry trends (free methods)
     */
    protected function getIndustryTrends(string $industry): array
    {
        // Check cache (global cache with null user_id)
        $cached = MarketInsight::whereNull('user_id')
            ->where('industry', $industry)
            ->where('insight_type', 'trends')
            ->where('expires_at', '>', now())
            ->first();
        
        if ($cached) {
            return $cached->data['trends'] ?? [];
        }
        
        // Use free methods to detect trends
        $trends = [];
        
        // Method 1: Pre-defined industry trends database
        $trends = array_merge($trends, $this->getPreDefinedTrends($industry));
        
        // Method 2: Scrape industry keywords (ethical, public data only)
        $trends = array_merge($trends, $this->scrapePublicTrends($industry));
        
        // Cache for 3 days (only if we have the migration fix)
        try {
            MarketInsight::create([
                'user_id' => null, // Global cache
                'industry' => $industry,
                'insight_type' => 'trends',
                'data' => ['trends' => $trends],
                'expires_at' => now()->addDays(3),
            ]);
        } catch (\Exception $e) {
            // Silently fail if cache creation fails
            Log::warning('Failed to cache trends: ' . $e->getMessage());
        }
        
        return array_slice($trends, 0, 10);
    }

    /**
     * Pre-defined industry trends
     */
    protected function getPreDefinedTrends(string $industry): array
    {
        $trendsByIndustry = [
            'fashion' => [
                'Sustainable fashion and eco-friendly materials',
                'Social commerce and Instagram shopping',
                'Influencer collaborations',
                'Video content and reels dominating engagement',
                'User-generated content campaigns',
            ],
            'food' => [
                'Health-conscious and plant-based options',
                'Behind-the-scenes kitchen content',
                'Recipe videos and cooking tutorials',
                'Food delivery integration',
                'Local sourcing and farm-to-table narratives',
            ],
            'tech' => [
                'AI and automation solutions',
                'Educational content and tutorials',
                'Product demo videos',
                'Customer success stories',
                'Thought leadership articles',
            ],
            'fitness' => [
                'Home workout solutions',
                'Wellness and mental health content',
                'Transformation stories',
                'Short-form workout videos',
                'Community challenges and engagement',
            ],
            'beauty' => [
                'Clean beauty and ingredient transparency',
                'Tutorial videos and how-to content',
                'Before/after transformations',
                'User reviews and testimonials',
                'Influencer partnerships',
            ],
        ];
        
        $normalized = strtolower($industry);
        
        foreach ($trendsByIndustry as $key => $trends) {
            if (str_contains($normalized, $key)) {
                return $trends;
            }
        }
        
        return [
            'Authentic storytelling and brand transparency',
            'Short-form video content',
            'Community engagement and user interaction',
            'Value-driven content over sales pitches',
            'Mobile-first content strategy',
        ];
    }

    /**
     * Scrape public trends (ethical, free)
     */
    protected function scrapePublicTrends(string $industry): array
    {
        try {
            // Use public RSS feeds or simple HTTP requests (no API key needed)
            // This is a placeholder - you can add specific sources
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get industry benchmarks
     */
    public function getIndustryBenchmarks(string $industry): array
    {
        // Pre-defined benchmarks based on industry research
        $benchmarks = [
            'fashion' => [
                'avg_engagement_rate' => 1.5,
                'avg_posts_per_week' => 7,
                'avg_followers_growth' => 3.2,
                'best_content_types' => ['image', 'carousel', 'reel'],
                'avg_response_time' => '2 hours',
            ],
            'food' => [
                'avg_engagement_rate' => 2.1,
                'avg_posts_per_week' => 10,
                'avg_followers_growth' => 2.8,
                'best_content_types' => ['video', 'image', 'story'],
                'avg_response_time' => '1 hour',
            ],
            'tech' => [
                'avg_engagement_rate' => 0.9,
                'avg_posts_per_week' => 5,
                'avg_followers_growth' => 2.1,
                'best_content_types' => ['video', 'link', 'carousel'],
                'avg_response_time' => '3 hours',
            ],
            'fitness' => [
                'avg_engagement_rate' => 1.8,
                'avg_posts_per_week' => 6,
                'avg_followers_growth' => 3.5,
                'best_content_types' => ['reel', 'video', 'image'],
                'avg_response_time' => '2 hours',
            ],
            'beauty' => [
                'avg_engagement_rate' => 2.3,
                'avg_posts_per_week' => 8,
                'avg_followers_growth' => 4.1,
                'best_content_types' => ['reel', 'tutorial', 'image'],
                'avg_response_time' => '1.5 hours',
            ],
        ];
        
        $normalized = strtolower($industry);
        
        foreach ($benchmarks as $key => $data) {
            if (str_contains($normalized, $key)) {
                return $data;
            }
        }
        
        // Default benchmarks
        return [
            'avg_engagement_rate' => 1.2,
            'avg_posts_per_week' => 5,
            'avg_followers_growth' => 2.5,
            'best_content_types' => ['image', 'video', 'carousel'],
            'avg_response_time' => '2 hours',
        ];
    }

    /**
     * Generate content opportunities
     */
    public function identifyContentOpportunities(int $userId, string $industry, array $userMetrics, array $competitorData = []): array
    {
        try {
            $benchmarks = $this->getIndustryBenchmarks($industry);
            $trends = $this->getIndustryTrends($industry);
            
            $opportunities = [];
            
            // Compare with benchmarks
            if (($userMetrics['posting_frequency'] ?? 0) < $benchmarks['avg_posts_per_week']) {
                $opportunities[] = [
                    'type' => 'posting_frequency',
                    'title' => 'Increase Posting Frequency',
                    'description' => "Industry average is {$benchmarks['avg_posts_per_week']} posts/week. You're currently posting less frequently.",
                    'priority' => 'high',
                    'action' => 'Aim for ' . ceil($benchmarks['avg_posts_per_week']) . ' posts per week',
                ];
            }
            
            if (($userMetrics['engagement_rate'] ?? 0) < $benchmarks['avg_engagement_rate']) {
                $opportunities[] = [
                    'type' => 'engagement',
                    'title' => 'Boost Engagement Rate',
                    'description' => "Your engagement rate is below industry average of {$benchmarks['avg_engagement_rate']}%",
                    'priority' => 'high',
                    'action' => 'Focus on interactive content and call-to-actions',
                ];
            }
            
            // Trend-based opportunities
            foreach (array_slice($trends, 0, 3) as $trend) {
                $opportunities[] = [
                    'type' => 'trend',
                    'title' => 'Leverage Trending Topic',
                    'description' => $trend,
                    'priority' => 'medium',
                    'action' => 'Create content around this trending topic',
                ];
            }
            
            // Content type opportunities
            $bestTypes = $benchmarks['best_content_types'];
            $opportunities[] = [
                'type' => 'content_type',
                'title' => 'Optimize Content Mix',
                'description' => 'Top performing content types in your industry: ' . implode(', ', $bestTypes),
                'priority' => 'medium',
                'action' => 'Test these content formats in your next posts',
            ];
            
            return [
                'success' => true,
                'opportunities' => $opportunities,
                'benchmarks' => $benchmarks,
            ];
            
        } catch (\Exception $e) {
            Log::error('Opportunity analysis failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate market report
     */
    public function generateMarketReport(int $userId, string $industry, array $businessContext): array
    {
        $trends = $this->getIndustryTrends($industry);
        $benchmarks = $this->getIndustryBenchmarks($industry);
        
        // Get user's competitors if any
        $competitors = \App\Models\CompetitorAnalysis::where('user_id', $userId)->get();
        
        return [
            'success' => true,
            'report' => [
                'industry' => $industry,
                'generated_at' => now()->toDateTimeString(),
                'trends' => $trends,
                'benchmarks' => $benchmarks,
                'competitors_analyzed' => $competitors->count(),
                'market_health' => $this->calculateMarketHealth($trends, $benchmarks),
            ],
        ];
    }

    protected function calculateMarketHealth(array $trends, array $benchmarks): string
    {
        // Simple health calculation based on engagement rates
        $engagementRate = $benchmarks['avg_engagement_rate'] ?? 1.0;
        
        if ($engagementRate > 2.0) {
            return 'Excellent - High engagement market with strong opportunities';
        } elseif ($engagementRate > 1.5) {
            return 'Good - Healthy market with moderate competition';
        } elseif ($engagementRate > 1.0) {
            return 'Average - Competitive market requiring strong differentiation';
        } else {
            return 'Challenging - Lower engagement market, focus on niche targeting';
        }
    }
}
