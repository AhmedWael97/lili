<?php

namespace App\Services\Marketing\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SEMrushService
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://api.semrush.com';

    public function __construct()
    {
        $this->apiKey = config('services.semrush.api_key');
    }

    /**
     * Get domain overview (organic search, paid search, backlinks)
     */
    public function getDomainOverview(string $domain, string $database = 'us'): array
    {
        if (!$this->isConfigured()) {
            return $this->getMockData($domain);
        }

        try {
            $cacheKey = "semrush_domain_{$domain}_{$database}";
            
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($domain, $database) {
                $response = Http::timeout(30)->get($this->baseUrl, [
                    'type' => 'domain_ranks',
                    'key' => $this->apiKey,
                    'export_columns' => 'Dn,Rk,Or,Ot,Oc,Ad,At,Ac,Sh,Sv',
                    'domain' => $domain,
                    'database' => $database,
                ]);

                if (!$response->successful()) {
                    Log::warning('SEMrush API error: ' . $response->status());
                    return $this->getMockData($domain);
                }

                $data = $this->parseSemrushCSV($response->body());

                return [
                    'success' => true,
                    'domain' => $domain,
                    'organic_keywords' => $data['organic_keywords'] ?? 0,
                    'organic_traffic' => $data['organic_traffic'] ?? 0,
                    'organic_cost' => $data['organic_cost'] ?? 0,
                    'adwords_keywords' => $data['adwords_keywords'] ?? 0,
                    'adwords_traffic' => $data['adwords_traffic'] ?? 0,
                    'adwords_cost' => $data['adwords_cost'] ?? 0,
                ];
            });

        } catch (\Exception $e) {
            Log::error('SEMrush API error: ' . $e->getMessage());
            return $this->getMockData($domain);
        }
    }

    /**
     * Get top organic keywords for domain
     */
    public function getOrganicKeywords(string $domain, string $database = 'us', int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API not configured'];
        }

        try {
            $cacheKey = "semrush_keywords_{$domain}_{$database}_{$limit}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($domain, $database, $limit) {
                $response = Http::timeout(30)->get($this->baseUrl, [
                    'type' => 'domain_organic',
                    'key' => $this->apiKey,
                    'export_columns' => 'Ph,Po,Nq,Cp,Co,Tr',
                    'domain' => $domain,
                    'database' => $database,
                    'display_limit' => $limit,
                ]);

                if (!$response->successful()) {
                    return ['success' => false, 'error' => 'API request failed'];
                }

                $keywords = $this->parseSemrushKeywords($response->body());

                return [
                    'success' => true,
                    'keywords' => $keywords,
                ];
            });

        } catch (\Exception $e) {
            Log::error('SEMrush keywords error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get paid (AdWords) keywords for domain
     */
    public function getPaidKeywords(string $domain, string $database = 'us', int $limit = 10): array
    {
        if (!$this->isConfigured()) {
            return $this->getMockKeywords($limit, 'paid');
        }

        try {
            $cacheKey = "semrush_paid_keywords_{$domain}_{$database}_{$limit}";
            
            return Cache::remember($cacheKey, now()->addDays(7), function () use ($domain, $database, $limit) {
                $response = Http::timeout(30)->get($this->baseUrl, [
                    'type' => 'domain_adwords',
                    'key' => $this->apiKey,
                    'domain' => $domain,
                    'database' => $database,
                    'display_limit' => $limit,
                    'export_columns' => 'Ph,Po,Nq,Cp,Ur,Tr,Tc',
                ]);

                if (!$response->successful()) {
                    return $this->getMockKeywords($limit, 'paid');
                }

                return [
                    'success' => true,
                    'keywords' => $this->parseSemrushKeywords($response->body()),
                ];
            });

        } catch (\Exception $e) {
            Log::error('SEMrush paid keywords error: ' . $e->getMessage());
            return $this->getMockKeywords($limit, 'paid');
        }
    }

    /**
     * Parse SEMrush CSV response
     */
    protected function parseSemrushCSV(string $csv): array
    {
        $lines = explode("\n", trim($csv));
        if (count($lines) < 2) {
            return [];
        }

        $headers = str_getcsv($lines[0], ';');
        $values = str_getcsv($lines[1], ';');

        return array_combine($headers, $values) ?: [];
    }

    /**
     * Parse SEMrush keywords CSV
     */
    protected function parseSemrushKeywords(string $csv): array
    {
        $lines = explode("\n", trim($csv));
        if (count($lines) < 2) {
            return [];
        }

        $headers = str_getcsv($lines[0], ';');
        $keywords = [];

        for ($i = 1; $i < count($lines); $i++) {
            if (empty(trim($lines[$i]))) continue;
            
            $values = str_getcsv($lines[$i], ';');
            if (count($values) === count($headers)) {
                $keywords[] = array_combine($headers, $values);
            }
        }

        return $keywords;
    }

    /**
     * Mock data for testing
     */
    protected function getMockData(string $domain): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'organic_keywords' => rand(500, 5000),
            'organic_traffic' => rand(1000, 50000),
            'organic_cost' => rand(1000, 20000),
            'adwords_keywords' => rand(50, 500),
            'adwords_traffic' => rand(500, 10000),
            'adwords_cost' => rand(500, 5000),
            'mock' => true,
        ];
    }

    /**
     * Mock keywords for testing
     */
    protected function getMockKeywords(int $limit, string $type = 'organic'): array
    {
        $keywords = [];
        $sampleKeywords = [
            'online shopping', 'e-commerce platform', 'buy online', 'shop now',
            'free delivery', 'fashion online', 'electronics store', 'best deals',
            'discount shopping', 'mobile accessories', 'home decor', 'beauty products'
        ];

        for ($i = 0; $i < $limit; $i++) {
            $keywords[] = [
                'Ph' => $sampleKeywords[array_rand($sampleKeywords)],
                'Po' => rand(1, 100),
                'Nq' => rand(1000, 100000),
                'Cp' => number_format(rand(50, 500) / 100, 2),
                'Ur' => '/products/category-' . $i,
                'Tr' => rand(100, 10000),
                'Tc' => rand(100, 5000),
            ];
        }

        return [
            'success' => true,
            'keywords' => $keywords,
            'mock' => true,
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
