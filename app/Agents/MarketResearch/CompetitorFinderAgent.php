<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\Competitor;
use App\Models\ResearchRequest;
use App\Services\GoogleSearchService;
use App\Services\WebScraperService;

class CompetitorFinderAgent extends BaseAgent
{
    protected string $name = 'CompetitorFinderAgent';
    protected string $description = 'Finds competitors using Google Search and extracts basic information';

    private GoogleSearchService $googleSearch;
    private WebScraperService $scraper;

    public function __construct(
        GoogleSearchService $googleSearch,
        WebScraperService $scraper
    ) {
        $this->googleSearch = $googleSearch;
        $this->scraper = $scraper;
    }

    /**
     * Find competitors for a research request
     *
     * @param ResearchRequest $request
     * @return array
     */
    public function execute(...$params): array
    {
        /** @var ResearchRequest $request */
        $request = $params[0];

        $this->log("Starting competitor search for: {$request->business_idea} in {$request->location}");

        // Step 1: Find competitors using Google Search
        $searchResults = $this->googleSearch->searchCompetitors(
            $request->business_idea,
            $request->location
        );

        if (empty($searchResults)) {
            $this->log("No search results found. Using GPT-4 fallback.");
            return $this->fallbackToGPT($request);
        }

        // Step 2: Extract and save competitors
        $competitors = [];
        foreach ($searchResults as $index => $result) {
            if ($index >= 10) break; // Limit to top 10

            $competitor = $this->extractCompetitorInfo($result, $request);

            if ($competitor) {
                $competitors[] = $competitor;
                $this->log("Found competitor: {$competitor->name}");
            }
        }

        // Step 3: Find review site URLs for each competitor
        foreach ($competitors as $competitor) {
            $this->findReviewSiteUrls($competitor);
        }

        $this->log("Completed: Found " . count($competitors) . " competitors");

        return $competitors;
    }

    /**
     * Extract competitor information from search result
     *
     * @param array $result
     * @param ResearchRequest $request
     * @return Competitor|null
     */
    private function extractCompetitorInfo(array $result, ResearchRequest $request): ?Competitor
    {
        $url = $result['link'] ?? null;

        if (!$url) {
            return null;
        }

        // Extract basic info from search result
        $name = $result['title'] ?? 'Unknown';
        $name = $this->cleanCompanyName($name);
        $description = $result['snippet'] ?? '';

        // Try to scrape additional info from the website
        $websiteInfo = $this->scrapeWebsiteInfo($url);

        // Calculate relevance score based on search position and content
        $relevanceScore = 100 - ((int)($result['displayLink'] ?? 0) * 10);

        return Competitor::create([
            'research_request_id' => $request->id,
            'name' => $name,
            'website' => $url,
            'description' => $description,
            'location' => $websiteInfo['location'] ?? null,
            'relevance_score' => max(0, $relevanceScore),
        ]);
    }

    /**
     * Clean company name from search title
     *
     * @param string $title
     * @return string
     */
    private function cleanCompanyName(string $title): string
    {
        // Remove common suffixes
        $title = preg_replace('/\s*[-|:]\s*.*$/', '', $title);
        $title = trim($title);

        return $title;
    }

    /**
     * Scrape additional info from competitor website
     *
     * @param string $url
     * @return array
     */
    private function scrapeWebsiteInfo(string $url): array
    {
        $info = [];

        try {
            $meta = $this->scraper->extractMetaTags($url);
            $jsonLd = $this->scraper->extractJsonLd($url);

            // Extract location from JSON-LD if available
            foreach ($jsonLd as $data) {
                if (isset($data['@type']) && $data['@type'] === 'LocalBusiness') {
                    $info['location'] = $data['address']['addressLocality'] ?? null;
                    break;
                }
            }

            $info['meta'] = $meta;
        } catch (\Exception $e) {
            $this->log("Error scraping {$url}: " . $e->getMessage());
        }

        return $info;
    }

    /**
     * Find review site URLs for a competitor
     *
     * @param Competitor $competitor
     * @return void
     */
    private function findReviewSiteUrls(Competitor $competitor): void
    {
        $reviewResults = $this->googleSearch->searchReviewSites($competitor->name);

        foreach ($reviewResults as $result) {
            $url = $result['link'] ?? null;

            if (!$url) continue;

            // Detect which platform
            if (str_contains($url, 'g2.com')) {
                $competitor->g2_url = $url;
            } elseif (str_contains($url, 'capterra.com')) {
                $competitor->capterra_url = $url;
            } elseif (str_contains($url, 'trustpilot.com')) {
                $competitor->trustpilot_url = $url;
            } elseif (str_contains($url, 'producthunt.com')) {
                $competitor->producthunt_url = $url;
            }
        }

        $competitor->save();
    }

    /**
     * Fallback to GPT-4 when Google Search fails
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function fallbackToGPT(ResearchRequest $request): array
    {
        $prompt = "
Based on the following business idea, suggest 10 realistic competitor companies that likely exist:

Business Idea: {$request->business_idea}
Location: {$request->location}

Provide a JSON response with this structure:
{
  \"competitors\": [
    {
      \"name\": \"Company Name\",
      \"description\": \"Brief description of what they do\",
      \"likely_website\": \"www.example.com\"
    }
  ]
}

Focus on real companies that likely exist in this market. If you're unsure, provide educated guesses based on typical companies in this industry.
";

        $response = $this->callGPTJson($prompt);

        if (!$response || !isset($response['competitors'])) {
            return [];
        }

        $competitors = [];
        foreach ($response['competitors'] as $index => $comp) {
            if ($index >= 10) break;

            $competitor = Competitor::create([
                'research_request_id' => $request->id,
                'name' => $comp['name'] ?? 'Unknown',
                'website' => $comp['likely_website'] ?? null,
                'description' => $comp['description'] ?? '',
                'relevance_score' => 50, // Medium relevance for GPT fallback
            ]);

            $competitors[] = $competitor;
        }

        return $competitors;
    }
}
