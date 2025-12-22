<?php

namespace App\Services\Marketing\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AhrefsService
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://apiv2.ahrefs.com';

    public function __construct()
    {
        $this->apiKey = config('services.ahrefs.api_key');
    }

    /**
     * Get domain metrics (DR, backlinks, referring domains)
     */
    public function getDomainMetrics(string $domain): array
    {
        if (!$this->isConfigured()) {
            return $this->getMockData($domain);
        }

        try {
            $cacheKey = "ahrefs_domain_{$domain}";
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->timeout(30)->get("{$this->baseUrl}/domain-rating", [
                    'target' => $domain,
                    'mode' => 'domain',
                ]);

                if (!$response->successful()) {
                    Log::warning('Ahrefs API error: ' . $response->status());
                    return $this->getMockData($domain);
                }

                $data = $response->json();

                return [
                    'success' => true,
                    'domain' => $domain,
                    'domain_rating' => $data['domain_rating'] ?? 0,
                    'backlinks' => $data['backlinks'] ?? 0,
                    'referring_domains' => $data['refdomains'] ?? 0,
                ];
            });

        } catch (\Exception $e) {
            Log::error('Ahrefs API error: ' . $e->getMessage());
            return $this->getMockData($domain);
        }
    }

    /**
     * Get backlinks for domain
     */
    public function getBacklinks(string $domain, int $limit = 50): array
    {
        if (!$this->isConfigured()) {
            return $this->getMockBacklinks($domain, $limit);
        }

        try {
            $cacheKey = "ahrefs_backlinks_{$domain}_{$limit}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($domain, $limit) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->timeout(30)->get("{$this->baseUrl}/backlinks", [
                    'target' => $domain,
                    'mode' => 'domain',
                    'limit' => $limit,
                    'order_by' => 'domain_rating:desc',
                ]);

                if (!$response->successful()) {
                    return $this->getMockBacklinks($domain, $limit);
                }

                $data = $response->json();

                return [
                    'success' => true,
                    'backlinks' => $data['backlinks'] ?? [],
                ];
            });

        } catch (\Exception $e) {
            Log::error('Ahrefs backlinks error: ' . $e->getMessage());
            return $this->getMockBacklinks($domain, $limit);
        }
    }

    /**
     * Get top pages by traffic
     */
    public function getTopPages(string $domain, int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API not configured'];
        }

        try {
            $cacheKey = "ahrefs_pages_{$domain}_{$limit}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($domain, $limit) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->timeout(30)->get("{$this->baseUrl}/pages", [
                    'target' => $domain,
                    'mode' => 'domain',
                    'limit' => $limit,
                    'order_by' => 'organic_traffic:desc',
                ]);

                if (!$response->successful()) {
                    return ['success' => false, 'error' => 'API request failed'];
                }

                $data = $response->json();

                return [
                    'success' => true,
                    'pages' => $data['pages'] ?? [],
                ];
            });

        } catch (\Exception $e) {
            Log::error('Ahrefs pages error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mock data for testing
     */
    protected function getMockData(string $domain): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'domain_rating' => rand(20, 80),
            'backlinks' => rand(1000, 100000),
            'referring_domains' => rand(100, 5000),
            'mock' => true,
        ];
    }

    /**
     * Mock backlinks for testing
     */
    protected function getMockBacklinks(string $domain, int $limit): array
    {
        $backlinks = [];
        $sampleDomains = [
            'techcrunch.com', 'forbes.com', 'cnn.com', 'bbc.com', 'reuters.com',
            'medium.com', 'reddit.com', 'quora.com', 'linkedin.com', 'twitter.com'
        ];

        for ($i = 0; $i < $limit; $i++) {
            $sourceDomain = $sampleDomains[array_rand($sampleDomains)];
            $backlinks[] = [
                'source_url' => "https://{$sourceDomain}/article-{$i}",
                'target_url' => "https://{$domain}/page-{$i}",
                'anchor_text' => 'Read more about ' . $domain,
                'domain_rating' => rand(30, 90),
                'url_rating' => rand(20, 80),
                'link_type' => rand(0, 1) ? 'dofollow' : 'nofollow',
                'first_seen' => now()->subDays(rand(30, 365))->toDateTimeString(),
                'last_seen' => now()->subDays(rand(1, 30))->toDateTimeString(),
            ];
        }

        return [
            'success' => true,
            'backlinks' => $backlinks,
            'mock' => true,
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
