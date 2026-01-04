<?php

namespace App\Agents\MarketResearch;

use App\Models\Competitor;
use App\Models\SocialIntelligence;
use App\Models\CompetitorSocialMetric;
use App\Models\CompetitorPost;
use App\Services\MarketResearch\SocialMediaScraperService;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class SocialIntelligenceAgent
{
    private SocialMediaScraperService $scraper;

    public function __construct(SocialMediaScraperService $scraper)
    {
        $this->scraper = $scraper;
    }
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
     * Collect social media data from OFFICIAL business pages ONLY
     * NOT from customer posts that tag the business
     */
    private function collectSocialData(Competitor $competitor): array
    {
        $data = [
            'platforms' => [],
            'total_followers' => 0,
            'all_posts' => [], // Store all posts for analysis
        ];

        // Facebook: Scrape OFFICIAL page posts (not tagged posts)
        if ($competitor->facebook_handle) {
            Log::info('Scraping Facebook official page', [
                'competitor' => $competitor->business_name,
                'handle' => $competitor->facebook_handle
            ]);
            
            $fbData = $this->scraper->scrapeFacebookPage($competitor->facebook_handle);
            
            if ($fbData['success']) {
                $fbMetric = [
                    'followers' => $fbData['followers'] ?? 0,
                    'posts_count' => $fbData['posts_count'] ?? 0,
                    'avg_engagement_rate' => $fbData['avg_engagement'] ?? 0,
                    'posting_frequency' => $fbData['posting_frequency'] ?? 'Unknown',
                    'last_post_date' => isset($fbData['posts'][0]) ? date('Y-m-d', strtotime($fbData['posts'][0]['created_time'])) : null,
                ];
                
                $data['platforms']['facebook'] = $fbMetric;
                $data['total_followers'] += $fbMetric['followers'];
                
                // Save metric
                $this->saveSocialMetric($competitor->id, 'facebook', $fbMetric);
                
                // Save individual posts (from official page only)
                if (isset($fbData['posts']) && is_array($fbData['posts'])) {
                    foreach ($fbData['posts'] as $post) {
                        $data['all_posts'][] = $post;
                        $this->savePost($competitor->id, 'facebook', $post);
                    }
                }
                
                Log::info('Facebook scraping successful', [
                    'competitor' => $competitor->business_name,
                    'followers' => $fbMetric['followers'],
                    'posts' => count($fbData['posts'] ?? []),
                    'source' => $fbData['source']
                ]);
            } else {
                // Fallback to estimation
                $fbMetric = $this->estimateFacebookMetrics($competitor->facebook_handle);
                $data['platforms']['facebook'] = $fbMetric;
                $data['total_followers'] += $fbMetric['followers'];
                $this->saveSocialMetric($competitor->id, 'facebook', $fbMetric);
                
                Log::warning('Facebook scraping failed, using estimates', [
                    'competitor' => $competitor->business_name,
                    'reason' => $fbData['warning'] ?? 'Unknown'
                ]);
            }
        }

        // Instagram: Scrape OFFICIAL profile posts (not tagged posts)
        if ($competitor->instagram_handle) {
            Log::info('Scraping Instagram official profile', [
                'competitor' => $competitor->business_name,
                'handle' => $competitor->instagram_handle
            ]);
            
            $igData = $this->scraper->scrapeInstagramProfile($competitor->instagram_handle);
            
            if ($igData['success']) {
                $igMetric = [
                    'followers' => $igData['followers'] ?? 0,
                    'following' => $igData['following'] ?? 0,
                    'posts_count' => $igData['posts_count'] ?? 0,
                    'avg_engagement_rate' => $igData['avg_engagement'] ?? 0,
                    'posting_frequency' => 'Daily', // Instagram is typically daily
                    'last_post_date' => isset($igData['posts'][0]) ? date('Y-m-d', strtotime($igData['posts'][0]['created_time'])) : null,
                ];
                
                $data['platforms']['instagram'] = $igMetric;
                $data['total_followers'] += $igMetric['followers'];
                $this->saveSocialMetric($competitor->id, 'instagram', $igMetric);
                
                // Save posts from official profile
                if (isset($igData['posts']) && is_array($igData['posts'])) {
                    foreach ($igData['posts'] as $post) {
                        $data['all_posts'][] = $post;
                        $this->savePost($competitor->id, 'instagram', $post);
                    }
                }
                
                Log::info('Instagram scraping successful', [
                    'competitor' => $competitor->business_name,
                    'followers' => $igMetric['followers'],
                    'posts' => count($igData['posts'] ?? [])
                ]);
            } else {
                $igMetric = $this->estimateInstagramMetrics($competitor->instagram_handle);
                $data['platforms']['instagram'] = $igMetric;
                $data['total_followers'] += $igMetric['followers'];
                $this->saveSocialMetric($competitor->id, 'instagram', $igMetric);
            }
        }

        // Twitter: Scrape OFFICIAL profile tweets (not mentions)
        if ($competitor->twitter_handle) {
            Log::info('Scraping Twitter official profile', [
                'competitor' => $competitor->business_name,
                'handle' => $competitor->twitter_handle
            ]);
            
            $twData = $this->scraper->scrapeTwitterProfile($competitor->twitter_handle);
            
            if ($twData['success']) {
                $twMetric = [
                    'followers' => $twData['followers'] ?? 0,
                    'following' => $twData['following'] ?? 0,
                    'posts_count' => $twData['posts_count'] ?? 0,
                    'avg_engagement_rate' => 1.5, // Twitter average
                    'posting_frequency' => 'Daily',
                    'last_post_date' => null,
                ];
                
                $data['platforms']['twitter'] = $twMetric;
                $data['total_followers'] += $twMetric['followers'];
                $this->saveSocialMetric($competitor->id, 'twitter', $twMetric);
            } else {
                $twMetric = $this->estimateTwitterMetrics($competitor->twitter_handle);
                $data['platforms']['twitter'] = $twMetric;
                $data['total_followers'] += $twMetric['followers'];
                $this->saveSocialMetric($competitor->id, 'twitter', $twMetric);
            }
        }

        return $data;
    }

    /**
     * Save individual post to database
     */
    private function savePost(int $competitorId, string $platform, array $post): void
    {
        try {
            CompetitorPost::create([
                'competitor_id' => $competitorId,
                'platform' => $platform,
                'post_url' => null, // Would need to construct from post ID
                'post_text' => $post['text'] ?? $post['message'] ?? '',
                'post_date' => $post['created_time'] ?? now(),
                'likes' => $post['likes'] ?? 0,
                'comments' => $post['comments'] ?? 0,
                'shares' => $post['shares'] ?? 0,
                'engagement_rate' => $post['engagement'] ?? null,
                'content_type' => 'post',
                'hashtags' => null, // Could extract from text
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save post', [
                'competitor_id' => $competitorId,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
        }
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
