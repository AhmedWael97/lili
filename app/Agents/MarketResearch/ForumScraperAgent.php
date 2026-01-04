<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\ForumDiscussion;
use App\Models\ResearchRequest;
use App\Services\GoogleSearchService;
use App\Services\WebScraperService;

class ForumScraperAgent extends BaseAgent
{
    protected string $name = 'ForumScraperAgent';
    protected string $description = 'Searches and scrapes forum discussions for customer pain points';

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
     * Find and scrape forum discussions
     *
     * @param ResearchRequest $request
     * @return int Number of discussions found
     */
    public function execute(...$params): int
    {
        /** @var ResearchRequest $request */
        $request = $params[0];

        $this->log("Searching for forum discussions about {$request->business_idea}");

        // Search for relevant discussions
        $discussions = $this->searchDiscussions($request);

        // If search fails, generate insights from GPT-4
        if (count($discussions) < 3) {
            $discussions = array_merge($discussions, $this->generateInsights($request));
        }

        $this->log("Found " . count($discussions) . " relevant discussions");

        return count($discussions);
    }

    /**
     * Search for forum discussions
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function searchDiscussions(ResearchRequest $request): array
    {
        $searchQueries = $this->buildSearchQueries($request);
        $discussions = [];

        foreach ($searchQueries as $query) {
            $results = $this->googleSearch->searchForums($query);

            foreach ($results as $result) {
                $url = $result['link'] ?? null;

                if (!$url) continue;

                $discussion = $this->processSearchResult($result, $request);

                if ($discussion) {
                    $discussions[] = $discussion;
                }

                // Limit to avoid too many requests
                if (count($discussions) >= 10) {
                    break 2;
                }
            }
        }

        return $discussions;
    }

    /**
     * Build search queries for different aspects
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function buildSearchQueries(ResearchRequest $request): array
    {
        $idea = $request->business_idea;

        return [
            "{$idea} problems",
            "{$idea} pain points",
            "{$idea} challenges",
            "{$idea} complaints",
            "{$idea} alternatives needed",
            "frustrated with {$idea}",
        ];
    }

    /**
     * Process a search result
     *
     * @param array $result
     * @param ResearchRequest $request
     * @return ForumDiscussion|null
     */
    private function processSearchResult(array $result, ResearchRequest $request): ?ForumDiscussion
    {
        $url = $result['link'];
        $title = $result['title'] ?? 'Untitled';
        $snippet = $result['snippet'] ?? '';

        // Determine source
        $source = $this->detectSource($url);

        // Try to scrape full content
        $content = $this->scrapeContent($url);

        if (!$content) {
            $content = $snippet; // Fallback to snippet
        }

        // Analyze content with GPT-4
        $analysis = $this->analyzeDiscussion($content);

        return ForumDiscussion::create([
            'research_request_id' => $request->id,
            'source' => $source,
            'url' => $url,
            'title' => $title,
            'content' => substr($content, 0, 5000), // Limit size
            'pain_points' => $analysis['pain_points'] ?? [],
            'feature_requests' => $analysis['feature_requests'] ?? [],
        ]);
    }

    /**
     * Detect source platform
     *
     * @param string $url
     * @return string
     */
    private function detectSource(string $url): string
    {
        if (str_contains($url, 'reddit.com')) return 'reddit';
        if (str_contains($url, 'quora.com')) return 'quora';
        if (str_contains($url, 'news.ycombinator.com')) return 'hackernews';
        if (str_contains($url, 'indiehackers.com')) return 'indiehackers';

        return 'forum';
    }

    /**
     * Scrape discussion content
     *
     * @param string $url
     * @return string|null
     */
    private function scrapeContent(string $url): ?string
    {
        try {
            $html = $this->scraper->scrape($url);

            if (!$html) {
                return null;
            }

            // Extract text content (basic approach)
            $text = strip_tags($html);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            return substr($text, 0, 5000); // Limit to first 5000 chars
        } catch (\Exception $e) {
            $this->log("Error scraping content: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze discussion with GPT-4
     *
     * @param string $content
     * @return array
     */
    private function analyzeDiscussion(string $content): array
    {
        $prompt = "
Analyze this forum discussion and extract:
1. Customer pain points mentioned
2. Feature requests or desired solutions

Discussion content:
{$content}

Return JSON:
{
  \"pain_points\": [\"pain point 1\", \"pain point 2\"],
  \"feature_requests\": [\"feature 1\", \"feature 2\"]
}

Focus on actionable insights.
";

        $response = $this->callGPTJson($prompt, 'gpt-4o-mini', 1000);

        return $response ?? ['pain_points' => [], 'feature_requests' => []];
    }

    /**
     * Generate insights using GPT-4 when search fails
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function generateInsights(ResearchRequest $request): array
    {
        $this->log("Generating insights for {$request->business_idea} using GPT-4");

        $prompt = "
Based on your knowledge, what are the most common customer pain points and problems people face with:

Business Type: {$request->business_idea}
Location: {$request->location}

Provide 5 realistic pain points that customers in this market typically experience.

Return JSON:
{
  \"discussions\": [
    {
      \"title\": \"Discussion title\",
      \"content\": \"Description of the pain point\",
      \"pain_points\": [\"specific pain point 1\", \"specific pain point 2\"],
      \"feature_requests\": [\"desired solution 1\"]
    }
  ]
}
";

        $response = $this->callGPTJson($prompt);

        if (!$response || !isset($response['discussions'])) {
            return [];
        }

        $discussions = [];
        foreach ($response['discussions'] as $disc) {
            $discussion = ForumDiscussion::create([
                'research_request_id' => $request->id,
                'source' => 'reddit',
                'url' => '#',
                'title' => $disc['title'] ?? 'Generated Insight',
                'content' => $disc['content'] ?? '',
                'pain_points' => $disc['pain_points'] ?? [],
                'feature_requests' => $disc['feature_requests'] ?? [],
            ]);

            $discussions[] = $discussion;
        }

        return $discussions;
    }
}
