<?php

namespace App\Services\Marketing\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SimilarWebService
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://api.similarweb.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.similarweb.api_key');
    }

    /**
     * Get website traffic and engagement data
     */
    public function getWebsiteData(string $domain): array
    {
        if (!$this->isConfigured()) {
            return $this->getMockData($domain);
        }

        try {
            $cacheKey = "similarweb_{$domain}";
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain) {
                $response = Http::withHeaders([
                    'api-key' => $this->apiKey,
                ])->timeout(30)->get("{$this->baseUrl}/website/{$domain}/total-traffic-and-engagement/visits");

                if (!$response->successful()) {
                    Log::warning('SimilarWeb API error: ' . $response->status());
                    return $this->getMockData($domain);
                }

                $data = $response->json();

                return [
                    'success' => true,
                    'domain' => $domain,
                    'visits' => $data['visits'] ?? 0,
                    'bounce_rate' => $data['bounce_rate'] ?? 0,
                    'pages_per_visit' => $data['pages_per_visit'] ?? 0,
                    'avg_visit_duration' => $data['average_visit_duration'] ?? 0,
                ];
            });

        } catch (\Exception $e) {
            Log::error('SimilarWeb API error: ' . $e->getMessage());
            return $this->getMockData($domain);
        }
    }

    /**
     * Get competitor data
     */
    public function getCompetitorData(string $domain): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API not configured'];
        }

        try {
            $cacheKey = "similarweb_competitors_{$domain}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($domain) {
                $response = Http::withHeaders([
                    'api-key' => $this->apiKey,
                ])->timeout(30)->get("{$this->baseUrl}/website/{$domain}/similar-sites/similarsites");

                if (!$response->successful()) {
                    return ['success' => false, 'error' => 'API request failed'];
                }

                $data = $response->json();

                return [
                    'success' => true,
                    'competitors' => $data['similar_sites'] ?? [],
                ];
            });

        } catch (\Exception $e) {
            Log::error('SimilarWeb competitors error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mock data for testing without API key
     */
    protected function getMockData(string $domain): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'visits' => rand(10000, 500000),
            'bounce_rate' => rand(40, 70),
            'pages_per_visit' => rand(2, 5),
            'avg_visit_duration' => rand(60, 300),
            'mock' => true,
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
