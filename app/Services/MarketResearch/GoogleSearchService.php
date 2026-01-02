<?php

namespace App\Services\MarketResearch;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GoogleSearchService
{
    private Client $client;
    private string $apiKey;
    private string $searchEngineId;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30, // 30 seconds timeout
            'connect_timeout' => 10, // 10 seconds connection timeout
        ]);
        $this->apiKey = config('services.google.api_key');
        $this->searchEngineId = config('services.google.search_engine_id');
    }

    /**
     * Search for businesses using Google Custom Search API
     */
    public function search(string $query, int $numResults = 10): array
    {
        try {
            Log::info('Google Search API request', [
                'query' => $query,
                'numResults' => $numResults
            ]);

            $response = $this->client->get('https://www.googleapis.com/customsearch/v1', [
                'query' => [
                    'key' => $this->apiKey,
                    'cx' => $this->searchEngineId,
                    'q' => $query,
                    'num' => min($numResults, 10), // Max 10 per request
                ],
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            $results = $this->formatResults($data['items'] ?? []);
            
            Log::info('Google Search API success', [
                'query' => $query,
                'results_count' => count($results)
            ]);

            return $results;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Google Search API connection timeout', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            throw new \Exception('Google Search API connection timeout. Please try again later.');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Google Search API request error', [
                'message' => $e->getMessage(),
                'status_code' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
                'query' => $query
            ]);
            throw new \Exception('Google Search API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Google Search API error', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            throw $e;
        }
    }

    /**
     * Format search results into standardized structure
     */
    private function formatResults(array $items): array
    {
        $results = [];

        foreach ($items as $item) {
            $results[] = [
                'title' => $item['title'] ?? '',
                'url' => $item['link'] ?? '',
                'snippet' => $item['snippet'] ?? '',
                'display_url' => $item['displayLink'] ?? '',
            ];
        }

        return $results;
    }

    /**
     * Build search query for finding competitors
     */
    public function buildCompetitorSearchQuery(string $businessIdea, string $location): string
    {
        return "{$businessIdea} businesses in {$location}";
    }
}
