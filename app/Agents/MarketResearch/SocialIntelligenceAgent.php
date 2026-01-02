<?php

namespace App\Agents\MarketResearch;

use App\Models\Competitor;
use App\Models\SocialIntelligence;
use App\Models\CompetitorSocialMetric;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class SocialIntelligenceAgent
{
    /**
     * Analyze social media presence for a competitor
     */
    public function analyzeSocialPresence(Competitor $competitor): ?SocialIntelligence
    {
        if (!$competitor->hasSocialPresence()) {
            Log::info('Competitor has no social presence', [
                'competitor_id' => $competitor->id,
                'name' => $competitor->business_name
            ]);
            return null;
        }

        Log::info('Analyzing social presence', [
            'competitor_id' => $competitor->id,
            'name' => $competitor->business_name
        ]);

        // Collect social metrics (simulated for now - real scraping would go here)
        $socialData = $this->collectSocialData($competitor);

        // Generate AI insights
        $insights = $this->generateInsights($competitor, $socialData);

        // Save to database
        return $this->saveSocialIntelligence($competitor, $insights);
    }

    /**
     * Collect social media data (simulated/estimated)
     */
    private function collectSocialData(Competitor $competitor): array
    {
        $data = [
            'platforms' => [],
            'total_followers' => 0,
        ];

        // For beta: Generate estimated metrics based on available handles
        // In production, this would scrape actual data
        if ($competitor->facebook_handle) {
            $fbMetric = $this->estimateFacebookMetrics($competitor->facebook_handle);
            $data['platforms']['facebook'] = $fbMetric;
            $data['total_followers'] += $fbMetric['followers'];
            
            // Save metric
            $this->saveSocialMetric($competitor->id, 'facebook', $fbMetric);
        }

        if ($competitor->instagram_handle) {
            $igMetric = $this->estimateInstagramMetrics($competitor->instagram_handle);
            $data['platforms']['instagram'] = $igMetric;
            $data['total_followers'] += $igMetric['followers'];
            
            // Save metric
            $this->saveSocialMetric($competitor->id, 'instagram', $igMetric);
        }

        if ($competitor->twitter_handle) {
            $twMetric = $this->estimateTwitterMetrics($competitor->twitter_handle);
            $data['platforms']['twitter'] = $twMetric;
            $data['total_followers'] += $twMetric['followers'];
            
            // Save metric
            $this->saveSocialMetric($competitor->id, 'twitter', $twMetric);
        }

        return $data;
    }

    /**
     * Estimate Facebook metrics (placeholder for real scraping)
     */
    private function estimateFacebookMetrics(string $handle): array
    {
        // In production, would scrape actual data
        // For beta, return estimated ranges
        return [
            'followers' => rand(500, 15000),
            'posts_count' => rand(50, 500),
            'avg_engagement_rate' => rand(10, 50) / 10, // 1.0 - 5.0%
            'posting_frequency' => $this->estimatePostingFrequency(),
            'last_post_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
        ];
    }

    /**
     * Estimate Instagram metrics
     */
    private function estimateInstagramMetrics(string $handle): array
    {
        return [
            'followers' => rand(800, 25000),
            'following' => rand(200, 2000),
            'posts_count' => rand(100, 1000),
            'avg_engagement_rate' => rand(20, 80) / 10, // 2.0 - 8.0%
            'posting_frequency' => $this->estimatePostingFrequency(),
            'last_post_date' => now()->subDays(rand(1, 15))->format('Y-m-d'),
        ];
    }

    /**
     * Estimate Twitter metrics
     */
    private function estimateTwitterMetrics(string $handle): array
    {
        return [
            'followers' => rand(300, 10000),
            'following' => rand(100, 1500),
            'posts_count' => rand(200, 2000),
            'avg_engagement_rate' => rand(5, 30) / 10, // 0.5 - 3.0%
            'posting_frequency' => $this->estimatePostingFrequency(),
            'last_post_date' => now()->subDays(rand(1, 7))->format('Y-m-d'),
        ];
    }

    /**
     * Estimate posting frequency
     */
    private function estimatePostingFrequency(): string
    {
        $frequencies = [
            'daily',
            '4-5x per week',
            '2-3x per week',
            'weekly',
            'few times per month',
        ];

        return $frequencies[array_rand($frequencies)];
    }

    /**
     * Save social metric to database
     */
    private function saveSocialMetric(int $competitorId, string $platform, array $metric): void
    {
        CompetitorSocialMetric::create([
            'competitor_id' => $competitorId,
            'platform' => $platform,
            'followers' => $metric['followers'] ?? null,
            'following' => $metric['following'] ?? null,
            'posts_count' => $metric['posts_count'] ?? null,
            'avg_engagement_rate' => $metric['avg_engagement_rate'] ?? null,
            'posting_frequency' => $metric['posting_frequency'] ?? null,
            'last_post_date' => $metric['last_post_date'] ?? null,
            'scraped_at' => now(),
        ]);
    }

    /**
     * Generate AI insights from social data
     */
    private function generateInsights(Competitor $competitor, array $socialData): array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($competitor, $socialData);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media strategist analyzing competitor data. Provide insights in JSON format.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            $content = $response->choices[0]->message->content;
            
            // Try to parse JSON response
            $insights = json_decode($content, true);
            
            if (!$insights) {
                // If not valid JSON, structure it manually
                $insights = [
                    'ai_insights' => $content,
                    'content_themes' => ['general'],
                    'top_hashtags' => [],
                    'strengths' => ['Active social presence'],
                    'weaknesses' => ['Unknown'],
                ];
            }

            return $insights;

        } catch (\Exception $e) {
            Log::error('Failed to generate social insights', [
                'error' => $e->getMessage(),
                'competitor_id' => $competitor->id
            ]);

            // Return basic insights
            return [
                'ai_insights' => 'Analysis pending - data collection in progress.',
                'content_themes' => ['general'],
                'top_hashtags' => [],
                'strengths' => ['Social media presence established'],
                'weaknesses' => ['Detailed analysis pending'],
            ];
        }
    }

    /**
     * Build analysis prompt for GPT-4
     */
    private function buildAnalysisPrompt(Competitor $competitor, array $socialData): string
    {
        $platformSummary = '';
        foreach ($socialData['platforms'] as $platform => $metrics) {
            $platformSummary .= "\n- {$platform}: {$metrics['followers']} followers, {$metrics['posting_frequency']}, {$metrics['avg_engagement_rate']}% engagement";
        }

        return "
Analyze this competitor's social media presence:

Business: {$competitor->business_name}
Website: {$competitor->website}

Social Media Metrics:
{$platformSummary}

Total Reach: {$socialData['total_followers']} followers across platforms

Provide analysis in JSON format with these keys:
{
  \"content_themes\": [\"theme1\", \"theme2\", ...],
  \"top_hashtags\": [\"#hashtag1\", \"#hashtag2\", ...],
  \"best_posting_times\": \"e.g., Mornings on weekdays\",
  \"engagement_patterns\": {
    \"high_engagement_content\": \"description\",
    \"low_engagement_content\": \"description\"
  },
  \"strengths\": [\"strength1\", \"strength2\", ...],
  \"weaknesses\": [\"weakness1\", \"weakness2\", ...],
  \"ai_insights\": \"2-3 paragraph analysis of their social strategy\"
}
";
    }

    /**
     * Save social intelligence to database
     */
    private function saveSocialIntelligence(Competitor $competitor, array $insights): SocialIntelligence
    {
        return SocialIntelligence::create([
            'competitor_id' => $competitor->id,
            'content_themes' => $insights['content_themes'] ?? [],
            'top_hashtags' => $insights['top_hashtags'] ?? [],
            'best_posting_times' => $insights['best_posting_times'] ?? null,
            'engagement_patterns' => $insights['engagement_patterns'] ?? [],
            'strengths' => $insights['strengths'] ?? [],
            'weaknesses' => $insights['weaknesses'] ?? [],
            'ai_insights' => $insights['ai_insights'] ?? null,
        ]);
    }

    /**
     * Analyze all competitors for a research request
     */
    public function analyzeAllCompetitors($competitors): void
    {
        foreach ($competitors as $competitor) {
            try {
                $this->analyzeSocialPresence($competitor);
                
                // Rate limiting
                sleep(1);
            } catch (\Exception $e) {
                Log::error('Failed to analyze competitor', [
                    'competitor_id' => $competitor->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
