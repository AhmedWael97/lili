<?php

namespace App\Services\MarketAnalysis;

use App\Models\CompetitorAnalysis;
use App\Services\SocialBladeService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompetitorAnalysisService
{
    protected ?string $fbAccessToken = null;
    protected SocialBladeService $socialBlade;

    public function __construct()
    {
        $this->socialBlade = new SocialBladeService();
        $this->fbAccessToken = config('services.facebook.app_access_token');
    }

    /**
     * Analyze a competitor's Facebook page
     */
    public function analyzeCompetitor(int $userId, string $pageIdOrUrl, string $competitorName, ?string $manualFollowerCount = null): array
    {
        try {
            // Extract page ID from URL if needed
            $pageId = $this->extractPageId($pageIdOrUrl);
            
            // Check if we have recent cached data
            $cached = CompetitorAnalysis::where('user_id', $userId)
                ->where('facebook_page_id', $pageId)
                ->first();
            
            if ($cached && !$cached->needsRefresh()) {
                return [
                    'success' => true,
                    'data' => $cached,
                    'cached' => true,
                ];
            }
            
            // Fetch fresh data from Facebook
            $pageData = $this->fetchPageData($pageId);
            
            // Override with manual follower count if provided
            if ($manualFollowerCount) {
                $parsedCount = $this->parseFollowerCount($manualFollowerCount);
                $pageData['followers_count'] = $parsedCount;
                $pageData['fan_count'] = $parsedCount;
                $pageData['manual_input'] = true;
            }
            
            $followerCount = $pageData['followers_count'] ?? $pageData['fan_count'] ?? 0;
            $posts = $this->fetchRecentPosts($pageId);
            $engagementMetrics = $this->calculateEngagementMetrics($posts, $followerCount);
            $postingPatterns = $this->analyzePostingPatterns($posts, $followerCount);
            $contentStrategy = $this->analyzeContentStrategy($posts);
            
            // Save or update cache
            $analysis = CompetitorAnalysis::updateOrCreate(
                [
                    'user_id' => $userId,
                    'facebook_page_id' => $pageId,
                ],
                [
                    'competitor_name' => $competitorName,
                    'page_data' => $pageData,
                    'engagement_metrics' => $engagementMetrics,
                    'posting_patterns' => $postingPatterns,
                    'content_strategy' => $contentStrategy,
                    'last_analyzed_at' => now(),
                ]
            );
            
            return [
                'success' => true,
                'data' => $analysis,
                'cached' => false,
            ];
            
        } catch (\Exception $e) {
            Log::error('Competitor analysis failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract Facebook page ID from URL or return as-is
     */
    protected function extractPageId(string $input): string
    {
        if (preg_match('/facebook\.com\/(.+?)(?:\/|$)/', $input, $matches)) {
            return $matches[1];
        }
        return $input;
    }

    /**
     * Fetch page data from Facebook Graph API
     */
    protected function fetchPageData(string $pageId): array
    {
        Log::info("========== COLLECTING DATA FROM ALL SOURCES FOR: {$pageId} ==========");
        
        $allSourcesData = [
            'facebook_api' => null,
            'social_blade' => null,
            'web_scraping' => null,
        ];
        
        // 1. TRY FACEBOOK API (with token)
        try {
            if ($this->fbAccessToken) {
                Log::info("Source 1: Trying Facebook API with access token");
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                    'fields' => 'id,name,category,followers_count,fan_count,about,website,verification_status',
                    'access_token' => $this->fbAccessToken,
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (!isset($data['followers_count']) && isset($data['fan_count'])) {
                        $data['followers_count'] = $data['fan_count'];
                    }
                    $allSourcesData['facebook_api'] = $data;
                    Log::info("✓ Facebook API SUCCESS", ['followers' => $data['followers_count'] ?? 0]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("✗ Facebook API failed: " . $e->getMessage());
        }
        
        // 2. TRY FACEBOOK API (without token - public)
        if (!$allSourcesData['facebook_api']) {
            try {
                Log::info("Source 2: Trying Facebook API without token (public)");
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                    'fields' => 'id,name,fan_count,about',
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $data['followers_count'] = $data['fan_count'] ?? 0;
                    $allSourcesData['facebook_api'] = $data;
                    Log::info("✓ Facebook API PUBLIC SUCCESS", ['followers' => $data['followers_count']]);
                }
            } catch (\Exception $e) {
                Log::warning("✗ Facebook API public failed: " . $e->getMessage());
            }
        }
        
        // 3. TRY SOCIAL BLADE API
        try {
            Log::info("Source 3: Trying Social Blade API");
            $socialBladeData = $this->socialBlade->getFacebookStats($pageId);
            
            if ($socialBladeData && isset($socialBladeData['followers'])) {
                $allSourcesData['social_blade'] = $socialBladeData;
                Log::info("✓ Social Blade SUCCESS", ['followers' => $socialBladeData['followers']]);
            }
        } catch (\Exception $e) {
            Log::warning("✗ Social Blade failed: " . $e->getMessage());
        }
        
        // 4. TRY WEB SCRAPING
        try {
            Log::info("Source 4: Trying web scraping");
            $scrapedData = $this->scrapeFacebookPageData($pageId);
            
            if (isset($scrapedData['followers_count']) && $scrapedData['followers_count'] > 0) {
                $allSourcesData['web_scraping'] = $scrapedData;
                Log::info("✓ Web Scraping SUCCESS", ['followers' => $scrapedData['followers_count']]);
            }
        } catch (\Exception $e) {
            Log::warning("✗ Web Scraping failed: " . $e->getMessage());
        }
        
        // MERGE AND ANALYZE ALL DATA
        return $this->mergeMultiSourceData($pageId, $allSourcesData);
    }
    
    /**
     * Merge data from multiple sources intelligently
     */
    protected function mergeMultiSourceData(string $pageId, array $sources): array
    {
        Log::info("========== MERGING DATA FROM MULTIPLE SOURCES ==========");
        
        $mergedData = [
            'id' => $pageId,
            'name' => $pageId,
            'followers_count' => 0,
            'fan_count' => 0,
            'data_sources' => [],
            'data_quality' => 'none',
            'cross_validation' => [],
        ];
        
        // Collect all follower counts for cross-validation
        $followerCounts = [];
        
        // Process Facebook API data (highest priority for accuracy)
        if ($sources['facebook_api']) {
            $fb = $sources['facebook_api'];
            $mergedData['name'] = $fb['name'] ?? $pageId;
            $mergedData['category'] = $fb['category'] ?? null;
            $mergedData['about'] = $fb['about'] ?? '';
            $mergedData['website'] = $fb['website'] ?? null;
            $mergedData['verification_status'] = $fb['verification_status'] ?? null;
            $mergedData['followers_count'] = $fb['followers_count'] ?? 0;
            $mergedData['fan_count'] = $fb['fan_count'] ?? 0;
            $mergedData['data_sources'][] = 'facebook_api';
            $mergedData['data_quality'] = 'high';
            
            if ($mergedData['followers_count'] > 0) {
                $followerCounts['facebook_api'] = $mergedData['followers_count'];
            }
        }
        
        // Process Social Blade data
        if ($sources['social_blade']) {
            $sb = $sources['social_blade'];
            $mergedData['data_sources'][] = 'social_blade';
            
            // Use Social Blade as primary if Facebook failed
            if (!$sources['facebook_api']) {
                $mergedData['name'] = $sb['username'] ?? $pageId;
                $mergedData['followers_count'] = $sb['followers'] ?? 0;
                $mergedData['fan_count'] = $sb['followers'] ?? 0;
                $mergedData['data_quality'] = 'medium';
            }
            
            // Add to cross-validation
            if (isset($sb['followers']) && $sb['followers'] > 0) {
                $followerCounts['social_blade'] = $sb['followers'];
                $mergedData['social_blade_engagement_rate'] = $sb['engagement_rate'] ?? null;
            }
        }
        
        // Process web scraping data
        if ($sources['web_scraping']) {
            $ws = $sources['web_scraping'];
            $mergedData['data_sources'][] = 'web_scraping';
            
            // Use scraped data if no better source available
            if (!$sources['facebook_api'] && !$sources['social_blade']) {
                $mergedData['name'] = $ws['name'] ?? $pageId;
                $mergedData['followers_count'] = $ws['followers_count'] ?? 0;
                $mergedData['fan_count'] = $ws['followers_count'] ?? 0;
                $mergedData['data_quality'] = 'low';
            }
            
            // Add to cross-validation
            if (isset($ws['followers_count']) && $ws['followers_count'] > 0) {
                $followerCounts['web_scraping'] = $ws['followers_count'];
            }
        }
        
        // CROSS-VALIDATION: Compare data from different sources
        if (count($followerCounts) > 1) {
            $mergedData['cross_validation'] = $followerCounts;
            
            // Calculate variance to assess data reliability
            $avg = array_sum($followerCounts) / count($followerCounts);
            $variance = 0;
            foreach ($followerCounts as $count) {
                $variance += pow($count - $avg, 2);
            }
            $variance = $variance / count($followerCounts);
            $stdDev = sqrt($variance);
            $coefficientOfVariation = ($avg > 0) ? ($stdDev / $avg) * 100 : 0;
            
            $mergedData['data_reliability'] = [
                'sources_count' => count($followerCounts),
                'average' => round($avg),
                'std_deviation' => round($stdDev),
                'variation_percentage' => round($coefficientOfVariation, 2),
                'status' => $coefficientOfVariation < 5 ? 'highly_reliable' : 
                           ($coefficientOfVariation < 15 ? 'reliable' : 'needs_verification'),
            ];
            
            Log::info("Cross-validation results", $mergedData['data_reliability']);
        }
        
        // Store raw data from all sources for future reference
        $mergedData['raw_sources_data'] = $sources;
        
        Log::info("Final merged data", [
            'sources' => $mergedData['data_sources'],
            'followers' => $mergedData['followers_count'],
            'quality' => $mergedData['data_quality'],
        ]);
        
        return $mergedData;
    }
    
    /**
     * Scrape Facebook page public data as fallback
     */
    protected function scrapeFacebookPageData(string $pageId): array
    {
        Log::info("Scraping Facebook page: {$pageId}");
        
        try {
            $url = "https://www.facebook.com/{$pageId}";
            $response = Http::timeout(10)->get($url);
            
            Log::info("Scraping response: " . $response->status());
            
            if ($response->successful()) {
                $html = $response->body();
                
                // Try to extract page name from title tag
                $pageName = $pageId;
                if (preg_match('/<title>(.+?)\s*[|\-]\s*Facebook/i', $html, $nameMatches)) {
                    $pageName = trim($nameMatches[1]);
                    Log::info("Extracted page name: {$pageName}");
                }
                
                // Try to extract follower count from meta tags or page content
                // Format: "1.1M followers", "500K followers", etc.
                if (preg_match('/([0-9,.]+[KMB]?)\s*followers?/i', $html, $matches)) {
                    $followersText = $matches[1];
                    $followers = $this->parseFollowerCount($followersText);
                    
                    Log::info("Extracted followers: {$followersText} -> {$followers}");
                    
                    return [
                        'id' => $pageId,
                        'name' => $pageName,
                        'followers_count' => $followers,
                        'fan_count' => $followers,
                        'scraped' => true,
                    ];
                }
                
                Log::warning("Could not extract follower count from HTML");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to scrape Facebook page: ' . $e->getMessage());
        }
        
        // Final fallback
        Log::warning("Returning empty data for page: {$pageId}");
        return [
            'id' => $pageId,
            'name' => $pageId,
            'followers_count' => 0,
            'fan_count' => 0,
            'error' => 'Unable to fetch follower count. Facebook API access may be restricted.',
        ];
    }
    
    /**
     * Parse follower count from text (1.1M, 500K, etc.)
     */
    protected function parseFollowerCount(string $text): int
    {
        $text = str_replace(',', '', $text);
        
        if (stripos($text, 'M') !== false) {
            return (int)(floatval($text) * 1000000);
        } elseif (stripos($text, 'K') !== false) {
            return (int)(floatval($text) * 1000);
        } elseif (stripos($text, 'B') !== false) {
            return (int)(floatval($text) * 1000000000);
        }
        
        return (int)$text;
    }

    /**
     * Fetch recent posts from Facebook page
     */
    protected function fetchRecentPosts(string $pageId, int $limit = 25): array
    {
        try {
            // Try with access token first
            if ($this->fbAccessToken) {
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/posts", [
                    'fields' => 'id,message,created_time,type,likes.summary(true),comments.summary(true),shares',
                    'limit' => $limit,
                    'access_token' => $this->fbAccessToken,
                ]);
                
                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
            }
            
            // Try without access token (public posts)
            $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/posts", [
                'fields' => 'id,message,created_time,type',
                'limit' => $limit,
            ]);
            
            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            
            Log::warning('Unable to fetch posts for ' . $pageId . ': ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::warning('Failed to fetch competitor posts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate engagement metrics from posts
     */
    protected function calculateEngagementMetrics(array $posts, int $followerCount = 0): array
    {
        if (empty($posts)) {
            // If we can't fetch posts but have follower count, provide estimated metrics
            if ($followerCount > 0) {
                // Industry average engagement rate is 0.5-3%
                $estimatedEngagementRate = 1.5; // 1.5% average
                $estimatedEngagement = intval($followerCount * ($estimatedEngagementRate / 100));
                
                return [
                    'avg_likes' => intval($estimatedEngagement * 0.70), // 70% likes
                    'avg_comments' => intval($estimatedEngagement * 0.20), // 20% comments
                    'avg_shares' => intval($estimatedEngagement * 0.10), // 10% shares
                    'avg_engagement_rate' => $estimatedEngagementRate,
                    'total_posts' => 0,
                    'estimated' => true,
                ];
            }
            
            return [
                'avg_likes' => 0,
                'avg_comments' => 0,
                'avg_shares' => 0,
                'avg_engagement_rate' => 0,
                'total_posts' => 0,
            ];
        }
        
        $totalLikes = 0;
        $totalComments = 0;
        $totalShares = 0;
        
        foreach ($posts as $post) {
            $totalLikes += $post['likes']['summary']['total_count'] ?? 0;
            $totalComments += $post['comments']['summary']['total_count'] ?? 0;
            $totalShares += $post['shares']['count'] ?? 0;
        }
        
        $count = count($posts);
        
        return [
            'avg_likes' => round($totalLikes / $count, 2),
            'avg_comments' => round($totalComments / $count, 2),
            'avg_shares' => round($totalShares / $count, 2),
            'avg_engagement_rate' => round(($totalLikes + $totalComments + $totalShares) / $count, 2),
            'total_posts' => $count,
            'period_days' => 30,
        ];
    }

    /**
     * Analyze posting patterns
     */
    protected function analyzePostingPatterns(array $posts, int $followerCount = 0): array
    {
        if (empty($posts)) {
            // Provide estimated patterns based on industry standards
            if ($followerCount > 0) {
                return [
                    'posting_frequency' => '3-5 posts per week (estimated)',
                    'best_times' => [
                        '12:00 PM - Peak engagement time',
                        '3:00 PM - Afternoon activity',
                        '7:00 PM - Evening browsing',
                    ],
                    'estimated' => true,
                ];
            }
            
            return ['posting_frequency' => 'Unknown', 'best_times' => []];
        }
        
        $hourCounts = array_fill(0, 24, 0);
        $dayCounts = array_fill(0, 7, 0);
        
        foreach ($posts as $post) {
            if (isset($post['created_time'])) {
                $time = Carbon::parse($post['created_time']);
                $hourCounts[$time->hour]++;
                $dayCounts[$time->dayOfWeek]++;
            }
        }
        
        // Find top 3 posting hours
        arsort($hourCounts);
        $topHours = array_slice(array_keys($hourCounts), 0, 3);
        
        // Find top posting days
        arsort($dayCounts);
        $topDays = array_slice(array_keys($dayCounts), 0, 3);
        
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        return [
            'posting_frequency' => round(count($posts) / 30, 1) . ' posts per day',
            'best_posting_hours' => $topHours,
            'best_posting_days' => array_map(fn($d) => $daysOfWeek[$d], $topDays),
            'posts_last_30_days' => count($posts),
        ];
    }

    /**
     * Analyze content strategy
     */
    protected function analyzeContentStrategy(array $posts): array
    {
        if (empty($posts)) {
            return ['content_types' => [], 'avg_caption_length' => 0];
        }
        
        $types = [];
        $captionLengths = [];
        
        foreach ($posts as $post) {
            $type = $post['type'] ?? 'status';
            $types[$type] = ($types[$type] ?? 0) + 1;
            
            if (isset($post['message'])) {
                $captionLengths[] = strlen($post['message']);
            }
        }
        
        arsort($types);
        
        return [
            'content_types' => $types,
            'most_used_type' => array_key_first($types) ?? 'unknown',
            'avg_caption_length' => !empty($captionLengths) ? round(array_sum($captionLengths) / count($captionLengths)) : 0,
            'uses_hashtags' => $this->detectHashtagUsage($posts),
            'uses_emojis' => $this->detectEmojiUsage($posts),
        ];
    }

    protected function detectHashtagUsage(array $posts): bool
    {
        foreach ($posts as $post) {
            if (isset($post['message']) && preg_match('/#\w+/', $post['message'])) {
                return true;
            }
        }
        return false;
    }

    protected function detectEmojiUsage(array $posts): bool
    {
        foreach ($posts as $post) {
            if (isset($post['message']) && preg_match('/[\x{1F600}-\x{1F64F}]/u', $post['message'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compare user's metrics with competitor
     */
    public function compareWithCompetitor(array $userMetrics, CompetitorAnalysis $competitor): array
    {
        $compMetrics = $competitor->engagement_metrics;
        
        return [
            'engagement_comparison' => [
                'user' => $userMetrics['avg_engagement_rate'] ?? 0,
                'competitor' => $compMetrics['avg_engagement_rate'] ?? 0,
                'difference' => ($userMetrics['avg_engagement_rate'] ?? 0) - ($compMetrics['avg_engagement_rate'] ?? 0),
            ],
            'posting_frequency_comparison' => [
                'user' => $userMetrics['posts_per_week'] ?? 0,
                'competitor' => $competitor->posting_patterns['posts_last_30_days'] ?? 0,
            ],
            'recommendations' => $this->generateRecommendations($userMetrics, $competitor),
        ];
    }

    protected function generateRecommendations(array $userMetrics, CompetitorAnalysis $competitor): array
    {
        $recommendations = [];
        
        $compMetrics = $competitor->engagement_metrics;
        $compPatterns = $competitor->posting_patterns;
        
        if (($compMetrics['avg_engagement_rate'] ?? 0) > ($userMetrics['avg_engagement_rate'] ?? 0)) {
            $recommendations[] = "Your competitor has {$compMetrics['avg_engagement_rate']}% higher engagement. Consider analyzing their content style.";
        }
        
        if (!empty($compPatterns['best_posting_hours'])) {
            $hours = implode(', ', array_map(fn($h) => $h . ':00', $compPatterns['best_posting_hours']));
            $recommendations[] = "Competitor posts most at: {$hours}. Test these times for your content.";
        }
        
        $contentStrategy = $competitor->content_strategy;
        if ($contentStrategy['uses_hashtags'] ?? false) {
            $recommendations[] = "Competitor effectively uses hashtags. Consider incorporating relevant hashtags in your posts.";
        }
        
        return $recommendations;
    }
}
