<?php

namespace App\Services\MarketResearch;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WebScraperService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ]
        ]);
    }

    /**
     * Scrape a website and extract social media links
     */
    public function extractSocialProfiles(string $url): array
    {
        try {
            $html = $this->fetchPage($url);
            
            if (!$html) {
                return $this->getEmptySocialProfiles();
            }

            $crawler = new Crawler($html);

            return [
                'facebook' => $this->extractFacebookHandle($html),
                'instagram' => $this->extractInstagramHandle($html),
                'twitter' => $this->extractTwitterHandle($html),
                'linkedin' => $this->extractLinkedInUrl($html),
            ];
        } catch (\Exception $e) {
            Log::warning('Web scraping error', [
                'url' => $url,
                'message' => $e->getMessage()
            ]);

            return $this->getEmptySocialProfiles();
        }
    }

    /**
     * Fetch page HTML
     */
    private function fetchPage(string $url): ?string
    {
        try {
            $response = $this->client->get($url);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error('Failed to fetch page', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Extract Facebook profile/page handle
     */
    private function extractFacebookHandle(string $html): ?string
    {
        // Match various Facebook URL patterns
        $patterns = [
            '/facebook\.com\/([a-zA-Z0-9\.\-_]+)/',
            '/fb\.com\/([a-zA-Z0-9\.\-_]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $handle = $matches[1];
                
                // Filter out common non-page paths
                if (!in_array($handle, ['sharer', 'pages', 'profile.php', 'share', 'login'])) {
                    return $handle;
                }
            }
        }

        return null;
    }

    /**
     * Extract Instagram handle
     */
    private function extractInstagramHandle(string $html): ?string
    {
        if (preg_match('/instagram\.com\/([a-zA-Z0-9\._]+)/', $html, $matches)) {
            $handle = $matches[1];
            
            // Filter out common paths
            if (!in_array($handle, ['explore', 'accounts', 'p', 'reel', 'tv'])) {
                return $handle;
            }
        }

        return null;
    }

    /**
     * Extract Twitter/X handle
     */
    private function extractTwitterHandle(string $html): ?string
    {
        $patterns = [
            '/twitter\.com\/([a-zA-Z0-9_]+)/',
            '/x\.com\/([a-zA-Z0-9_]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $handle = $matches[1];
                
                // Filter out common paths
                if (!in_array($handle, ['home', 'explore', 'notifications', 'messages', 'intent', 'share'])) {
                    return $handle;
                }
            }
        }

        return null;
    }

    /**
     * Extract LinkedIn company URL
     */
    private function extractLinkedInUrl(string $html): ?string
    {
        if (preg_match('/linkedin\.com\/company\/([a-zA-Z0-9\-]+)/', $html, $matches)) {
            return "https://linkedin.com/company/{$matches[1]}";
        }

        return null;
    }

    /**
     * Extract business information from website
     */
    public function extractBusinessInfo(string $url): array
    {
        try {
            $html = $this->fetchPage($url);
            
            if (!$html) {
                return [];
            }

            $crawler = new Crawler($html);

            return [
                'phone' => $this->extractPhone($html),
                'address' => $this->extractAddress($crawler),
                'email' => $this->extractEmail($html),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to extract business info', ['url' => $url]);
            return [];
        }
    }

    /**
     * Extract phone number
     */
    private function extractPhone(string $html): ?string
    {
        // Common phone number patterns
        $patterns = [
            '/\+?1?\s*\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4}/',
            '/\+?\d{1,3}[\s\-]?\(?\d{2,3}\)?[\s\-]?\d{3,4}[\s\-]?\d{4}/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    /**
     * Extract address
     */
    private function extractAddress(Crawler $crawler): ?string
    {
        try {
            // Look for address in common HTML elements
            $addressSelectors = [
                'address',
                '[itemprop="address"]',
                '.address',
                '#address'
            ];

            foreach ($addressSelectors as $selector) {
                try {
                    $address = $crawler->filter($selector)->first()->text();
                    if ($address) {
                        return trim($address);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }

    /**
     * Extract email address
     */
    private function extractEmail(string $html): ?string
    {
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $html, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Get empty social profiles array
     */
    private function getEmptySocialProfiles(): array
    {
        return [
            'facebook' => null,
            'instagram' => null,
            'twitter' => null,
            'linkedin' => null,
        ];
    }
}
