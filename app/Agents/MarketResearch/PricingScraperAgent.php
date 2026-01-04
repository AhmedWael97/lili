<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\Competitor;
use App\Models\CompetitorPricing;
use App\Services\WebScraperService;

class PricingScraperAgent extends BaseAgent
{
    protected string $name = 'PricingScraperAgent';
    protected string $description = 'Scrapes pricing information from competitor websites';

    private WebScraperService $scraper;

    public function __construct(WebScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    /**
     * Scrape pricing for all competitors
     *
     * @param array $competitors
     * @return int Total pricing tiers found
     */
    public function execute(...$params): int
    {
        /** @var array $competitors */
        $competitors = $params[0];
        $totalPricing = 0;

        $this->log("Starting pricing scraping for " . count($competitors) . " competitors");

        foreach ($competitors as $competitor) {
            $pricingData = $this->scrapeCompetitorPricing($competitor);
            $totalPricing += count($pricingData);
        }

        $this->log("Completed: Found {$totalPricing} pricing tiers");

        return $totalPricing;
    }

    /**
     * Scrape pricing for a single competitor
     *
     * @param Competitor $competitor
     * @return array
     */
    private function scrapeCompetitorPricing(Competitor $competitor): array
    {
        if (!$competitor->website) {
            return $this->generateEstimatedPricing($competitor);
        }

        $this->log("Scraping pricing for {$competitor->name}");

        // Try common pricing URL patterns
        $pricingUrls = $this->getPossiblePricingUrls($competitor->website);

        foreach ($pricingUrls as $url) {
            if ($this->scraper->isAccessible($url)) {
                $pricing = $this->scrapePricingPage($url, $competitor);

                if (!empty($pricing)) {
                    return $pricing;
                }
            }
        }

        // Fallback to GPT-4 analysis
        return $this->generateEstimatedPricing($competitor);
    }

    /**
     * Get possible pricing page URLs
     *
     * @param string $baseUrl
     * @return array
     */
    private function getPossiblePricingUrls(string $baseUrl): array
    {
        $baseUrl = rtrim($baseUrl, '/');

        return [
            $baseUrl . '/pricing',
            $baseUrl . '/pricing/',
            $baseUrl . '/plans',
            $baseUrl . '/plans/',
            $baseUrl . '/pricing-plans',
            $baseUrl . '/price',
            $baseUrl . '/buy',
            $baseUrl . '/packages',
        ];
    }

    /**
     * Scrape pricing page
     *
     * @param string $url
     * @param Competitor $competitor
     * @return array
     */
    private function scrapePricingPage(string $url, Competitor $competitor): array
    {
        try {
            $html = $this->scraper->scrape($url);

            if (!$html) {
                return [];
            }

            // Use GPT-4 to extract pricing from HTML
            return $this->extractPricingWithGPT($html, $competitor);
        } catch (\Exception $e) {
            $this->log("Error scraping pricing page: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract pricing using GPT-4
     *
     * @param string $html
     * @param Competitor $competitor
     * @return array
     */
    private function extractPricingWithGPT(string $html, Competitor $competitor): array
    {
        // Clean and truncate HTML for GPT-4
        $html = strip_tags($html, '<div><span><p><h1><h2><h3><strong><b>');
        $html = substr($html, 0, 8000); // Limit size

        $prompt = "
Extract pricing information from this HTML content. Look for pricing tiers, plans, or packages.

HTML Content:
{$html}

Return JSON:
{
  \"pricing_tiers\": [
    {
      \"tier_name\": \"Basic\",
      \"price\": 29.99,
      \"billing_period\": \"monthly\",
      \"pricing_model\": \"subscription\",
      \"features\": [\"feature 1\", \"feature 2\"],
      \"is_popular\": false,
      \"currency\": \"USD\"
    }
  ]
}

IMPORTANT:
- If pricing is custom/quote-based or says 'Contact Sales', set price to null (not the string 'custom')
- If you cannot find pricing, return empty array
- Be as accurate as possible
";

        $response = $this->callGPTJson($prompt, 'gpt-4o', 2000);

        if (!$response || !isset($response['pricing_tiers'])) {
            return [];
        }

        $pricingData = [];
        foreach ($response['pricing_tiers'] as $tier) {
            $pricing = CompetitorPricing::create([
                'competitor_id' => $competitor->id,
                'tier_name' => $tier['tier_name'] ?? 'Standard',
                'price' => $tier['price'] ?? null,
                'billing_period' => $tier['billing_period'] ?? 'monthly',
                'pricing_model' => $tier['pricing_model'] ?? 'subscription',
                'features' => $tier['features'] ?? [],
                'is_popular' => $tier['is_popular'] ?? false,
                'currency' => $tier['currency'] ?? 'USD',
            ]);

            $pricingData[] = $pricing;
        }

        $this->log("Found " . count($pricingData) . " pricing tiers for {$competitor->name}");
        return $pricingData;
    }

    /**
     * Generate estimated pricing using GPT-4 knowledge
     *
     * @param Competitor $competitor
     * @return array
     */
    private function generateEstimatedPricing(Competitor $competitor): array
    {
        $this->log("Generating estimated pricing for {$competitor->name}");

        $prompt = "
Based on typical pricing in the industry, estimate realistic pricing tiers for this company:

Company: {$competitor->name}
Description: {$competitor->description}

Provide 2-4 realistic pricing tiers that would be typical for this type of business.

Return JSON:
{
  \"pricing_tiers\": [
    {
      \"tier_name\": \"Starter\",
      \"price\": 29.00,
      \"billing_period\": \"monthly\",
      \"pricing_model\": \"subscription\",
      \"features\": [\"feature 1\", \"feature 2\"],
      \"description\": \"For individuals\",
      \"is_popular\": false,
      \"currency\": \"USD\"
    }
  ]
}

IMPORTANT: For enterprise/custom pricing tiers, set price to null (not the string 'custom')
";

        $response = $this->callGPTJson($prompt);

        if (!$response || !isset($response['pricing_tiers'])) {
            return [];
        }

        $pricingData = [];
        foreach ($response['pricing_tiers'] as $tier) {
            $pricing = CompetitorPricing::create([
                'competitor_id' => $competitor->id,
                'tier_name' => $tier['tier_name'] ?? 'Standard',
                'price' => $tier['price'] ?? null,
                'billing_period' => $tier['billing_period'] ?? 'monthly',
                'pricing_model' => $tier['pricing_model'] ?? 'subscription',
                'features' => $tier['features'] ?? [],
                'description' => $tier['description'] ?? '',
                'is_popular' => $tier['is_popular'] ?? false,
                'currency' => $tier['currency'] ?? 'USD',
            ]);

            $pricingData[] = $pricing;
        }

        return $pricingData;
    }
}
