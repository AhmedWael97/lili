<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSearchService
{
    private Client $client;
    private string $apiKey;
    private string $searchEngineId;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->apiKey = config('services.google.api_key', '');
        $this->searchEngineId = config('services.google.search_engine_id', '');
    }

    /**
     * Search Google Custom Search API
     *
     * @param string $query
     * @param int $numResults
     * @return array
     */
    public function search(string $query, int $numResults = 10): array
    {
        // Cache key based on query
        $cacheKey = 'google_search_' . md5($query . $numResults);

        return Cache::remember($cacheKey, 86400, function () use ($query, $numResults) {
            if (empty($this->apiKey) || empty($this->searchEngineId)) {
                Log::warning('Google Search API credentials not configured');
                return [];
            }

            try {
                $response = $this->client->get('https://www.googleapis.com/customsearch/v1', [
                    'query' => [
                        'key' => $this->apiKey,
                        'cx' => $this->searchEngineId,
                        'q' => $query,
                        'num' => min($numResults, 10), // Max 10 per request
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                return $data['items'] ?? [];
            } catch (\Exception $e) {
                Log::error('Google Search API error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Search for competitors in a specific location
     *
     * @param string $businessIdea
     * @param string $location
     * @return array
     */
    public function searchCompetitors(string $businessIdea, string $location): array
    {
        $query = "{$businessIdea} companies in {$location}";
        return $this->search($query, 10);
    }

    /**
     * Search for review sites mentioning a competitor
     *
     * @param string $competitorName
     * @return array
     */
    public function searchReviewSites(string $competitorName): array
    {
        $query = "{$competitorName} reviews site:g2.com OR site:capterra.com OR site:trustpilot.com";
        return $this->search($query, 5);
    }

    /**
     * Search for forum discussions
     *
     * @param string $topic
     * @return array
     */
    public function searchForums(string $topic): array
    {
        $query = "{$topic} problems site:reddit.com OR site:quora.com OR site:indiehackers.com";
        return $this->search($query, 10);
    }

    /**
     * Extract domain from URL
     *
     * @param string $url
     * @return string|null
     */
    public function extractDomain(string $url): ?string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? null;
    }
}
