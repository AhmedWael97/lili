<?php

namespace App\Services\MarketAnalysis;

use App\Models\CompetitorAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompetitorAnalysisService
{
    protected $fbAccessToken;

    public function __construct()
    {
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
            
            $posts = $this->fetchRecentPosts($pageId);
            $engagementMetrics = $this->calculateEngagementMetrics($posts);
            $postingPatterns = $this->analyzePostingPatterns($posts);
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
        Log::info("Fetching page data for: {$pageId}");
        
        try {
            // Try with access token first
            if ($this->fbAccessToken) {
                Log::info("Trying with access token");
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                    'fields' => 'id,name,category,followers_count,fan_count,about,website,verification_status',
                    'access_token' => $this->fbAccessToken,
                ]);
                
                Log::info("Facebook API Response (with token): " . $response->status() . " - " . $response->body());
                
                if ($response->successful()) {
                    $data = $response->json();
                    // Use fan_count if followers_count is not available
                    if (!isset($data['followers_count']) && isset($data['fan_count'])) {
                        $data['followers_count'] = $data['fan_count'];
                    }
                    Log::info("Successfully fetched with token", $data);
                    return $data;
                }
                
                Log::warning('Facebook API error with token: ' . $response->body());
            }
            
            // Try without access token (public data) - more limited but works
            Log::info("Trying without access token (public API)");
            $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                'fields' => 'id,name,fan_count,about',
            ]);
            
            Log::info("Facebook API Response (no token): " . $response->status() . " - " . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                // Map fan_count to followers_count
                $data['followers_count'] = $data['fan_count'] ?? 0;
                Log::info("Successfully fetched without token", $data);
                return $data;
            }
            
            throw new \Exception('Failed to fetch page data: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Facebook API failed for page ' . $pageId . ': ' . $e->getMessage());
            
            // Use web scraping as fallback to get public follower count
            Log::info("Falling back to web scraping");
            return $this->scrapeFacebookPageData($pageId);
        }
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
    protected function calculateEngagementMetrics(array $posts): array
    {
        if (empty($posts)) {
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
    protected function analyzePostingPatterns(array $posts): array
    {
        if (empty($posts)) {
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
