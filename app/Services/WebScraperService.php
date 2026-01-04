<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebScraperService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // For development - enable in production
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ],
        ]);
    }

    /**
     * Scrape a webpage and return HTML content
     *
     * @param string $url
     * @param int $cacheDuration Cache duration in seconds (default: 24 hours)
     * @return string|null
     */
    public function scrape(string $url, int $cacheDuration = 86400): ?string
    {
        $cacheKey = 'scrape_' . md5($url);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($url) {
            try {
                // Add delay to be respectful
                sleep(1);

                $response = $this->client->get($url);
                return $response->getBody()->getContents();
            } catch (\Exception $e) {
                Log::error("Scraping error for {$url}: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Scrape and return a Crawler instance
     *
     * @param string $url
     * @return Crawler|null
     */
    public function crawl(string $url): ?Crawler
    {
        $html = $this->scrape($url);

        if (!$html) {
            return null;
        }

        return new Crawler($html);
    }

    /**
     * Extract text content from a selector
     *
     * @param string $url
     * @param string $selector
     * @return array
     */
    public function extractText(string $url, string $selector): array
    {
        $crawler = $this->crawl($url);

        if (!$crawler) {
            return [];
        }

        $results = [];
        $crawler->filter($selector)->each(function (Crawler $node) use (&$results) {
            $results[] = trim($node->text());
        });

        return $results;
    }

    /**
     * Extract links from a page
     *
     * @param string $url
     * @param string $selector
     * @return array
     */
    public function extractLinks(string $url, string $selector = 'a'): array
    {
        $crawler = $this->crawl($url);

        if (!$crawler) {
            return [];
        }

        $links = [];
        $crawler->filter($selector)->each(function (Crawler $node) use (&$links) {
            if ($node->attr('href')) {
                $links[] = $node->attr('href');
            }
        });

        return $links;
    }

    /**
     * Extract meta tags
     *
     * @param string $url
     * @return array
     */
    public function extractMetaTags(string $url): array
    {
        $crawler = $this->crawl($url);

        if (!$crawler) {
            return [];
        }

        $meta = [];

        // Title
        try {
            $meta['title'] = $crawler->filter('title')->text();
        } catch (\Exception $e) {
            $meta['title'] = null;
        }

        // Description
        try {
            $meta['description'] = $crawler->filter('meta[name="description"]')->attr('content');
        } catch (\Exception $e) {
            $meta['description'] = null;
        }

        // OG tags
        try {
            $meta['og_title'] = $crawler->filter('meta[property="og:title"]')->attr('content');
        } catch (\Exception $e) {
            $meta['og_title'] = null;
        }

        try {
            $meta['og_description'] = $crawler->filter('meta[property="og:description"]')->attr('content');
        } catch (\Exception $e) {
            $meta['og_description'] = null;
        }

        return $meta;
    }

    /**
     * Check if a URL is accessible
     *
     * @param string $url
     * @return bool
     */
    public function isAccessible(string $url): bool
    {
        try {
            $response = $this->client->head($url);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract JSON-LD data from a page
     *
     * @param string $url
     * @return array
     */
    public function extractJsonLd(string $url): array
    {
        $crawler = $this->crawl($url);

        if (!$crawler) {
            return [];
        }

        $jsonLdData = [];

        $crawler->filter('script[type="application/ld+json"]')->each(function (Crawler $node) use (&$jsonLdData) {
            try {
                $json = json_decode($node->text(), true);
                if ($json) {
                    $jsonLdData[] = $json;
                }
            } catch (\Exception $e) {
                // Skip invalid JSON
            }
        });

        return $jsonLdData;
    }
}
