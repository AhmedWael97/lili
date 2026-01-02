<?php

namespace App\Agents\MarketResearch;

use App\Models\Competitor;
use App\Models\ResearchRequest;
use App\Services\MarketResearch\GoogleSearchService;
use App\Services\MarketResearch\WebScraperService;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class CompetitorFinderAgent
{
    private GoogleSearchService $googleSearch;
    private WebScraperService $webScraper;

    public function __construct(
        GoogleSearchService $googleSearch,
        WebScraperService $webScraper
    ) {
        $this->googleSearch = $googleSearch;
        $this->webScraper = $webScraper;
    }

    /**
     * Find competitors for a given research request
     */
    public function findCompetitors(ResearchRequest $request): array
    {
        Log::info('Starting competitor search', [
            'request_id' => $request->id,
            'business_idea' => $request->business_idea,
            'location' => $request->location
        ]);

        // Step 1: Extract keywords from business idea using AI
        $keywords = $this->extractKeywords($request->business_idea);

        // Step 2: Search Google for competitors
        $searchQuery = $this->buildSearchQuery($keywords, $request->location);
        $searchResults = $this->googleSearch->search($searchQuery, 15);

        if (empty($searchResults)) {
            Log::warning('No search results found', ['query' => $searchQuery]);
            return [];
        }

        // Step 3: Extract social profiles from websites
        $competitors = [];
        foreach ($searchResults as $result) {
            $competitor = $this->processSearchResult($result, $request->id);
            
            if ($competitor) {
                $competitors[] = $competitor;
            }

            // Respect rate limits
            sleep(1);
        }

        // Step 4: Rank competitors by relevance
        $rankedCompetitors = $this->rankCompetitors($competitors, $request->business_idea);

        Log::info('Competitor search completed', [
            'request_id' => $request->id,
            'competitors_found' => count($rankedCompetitors)
        ]);

        return $rankedCompetitors;
    }

    /**
     * Extract keywords from business idea using GPT-4
     */
    private function extractKeywords(string $businessIdea): array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business analyst. Extract the key searchable keywords from a business idea. Return ONLY a comma-separated list of 3-5 keywords, no explanations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Business idea: {$businessIdea}\n\nExtract keywords:"
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 50,
            ]);

            $keywordsStr = $response->choices[0]->message->content;
            $keywords = array_map('trim', explode(',', $keywordsStr));

            return array_filter($keywords);
        } catch (\Exception $e) {
            Log::error('Failed to extract keywords', ['error' => $e->getMessage()]);
            
            // Fallback: use the business idea as-is
            return [$businessIdea];
        }
    }

    /**
     * Build search query from keywords and location
     */
    private function buildSearchQuery(array $keywords, string $location): string
    {
        $keywordStr = implode(' ', $keywords);
        return "{$keywordStr} businesses in {$location}";
    }

    /**
     * Process a single search result
     */
    private function processSearchResult(array $result, int $requestId): ?array
    {
        try {
            $url = $result['url'];

            // Extract social profiles from website
            $socialProfiles = $this->webScraper->extractSocialProfiles($url);

            // Extract business information
            $businessInfo = $this->webScraper->extractBusinessInfo($url);

            return [
                'research_request_id' => $requestId,
                'business_name' => $this->cleanBusinessName($result['title']),
                'website' => $url,
                'facebook_handle' => $socialProfiles['facebook'],
                'instagram_handle' => $socialProfiles['instagram'],
                'twitter_handle' => $socialProfiles['twitter'],
                'linkedin_url' => $socialProfiles['linkedin'],
                'phone' => $businessInfo['phone'] ?? null,
                'address' => $businessInfo['address'] ?? null,
                'relevance_score' => 0, // Will be set in ranking
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to process search result', [
                'url' => $result['url'] ?? '',
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Clean business name from search result title
     */
    private function cleanBusinessName(string $title): string
    {
        // Remove common suffixes from search result titles
        $patterns = [
            '/ - .*$/',
            '/ \| .*$/',
            '/ – .*$/',
        ];

        $cleaned = $title;
        foreach ($patterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $cleaned);
        }

        return trim($cleaned);
    }

    /**
     * Rank competitors by relevance using AI
     */
    private function rankCompetitors(array $competitors, string $businessIdea): array
    {
        if (empty($competitors)) {
            return [];
        }

        try {
            // Prepare competitor list for AI analysis
            $competitorList = array_map(function ($comp, $index) {
                return ($index + 1) . ". {$comp['business_name']} - {$comp['website']}";
            }, $competitors, array_keys($competitors));

            $competitorListStr = implode("\n", $competitorList);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business analyst. Rank competitors by relevance to a business idea. Return ONLY comma-separated numbers representing the ranking (e.g., "3,1,5,2,4" means item 3 is most relevant, then 1, then 5, etc.)'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Business idea: {$businessIdea}\n\nCompetitors:\n{$competitorListStr}\n\nRank by relevance (return only numbers):"
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 100,
            ]);

            $rankingStr = $response->choices[0]->message->content;
            $ranking = array_map('trim', explode(',', $rankingStr));

            // Apply ranking scores
            $rankedCompetitors = [];
            foreach ($ranking as $position => $competitorIndex) {
                $index = (int)$competitorIndex - 1;
                
                if (isset($competitors[$index])) {
                    $competitors[$index]['relevance_score'] = 100 - ($position * 5);
                    $rankedCompetitors[] = $competitors[$index];
                }
            }

            // Add any remaining competitors that weren't ranked
            foreach ($competitors as $index => $competitor) {
                if (!isset($competitor['relevance_score']) || $competitor['relevance_score'] === 0) {
                    $competitor['relevance_score'] = 10;
                    $rankedCompetitors[] = $competitor;
                }
            }

            return $rankedCompetitors;
        } catch (\Exception $e) {
            Log::error('Failed to rank competitors', ['error' => $e->getMessage()]);
            
            // Fallback: return competitors in original order with default scores
            return array_map(function ($comp, $index) {
                $comp['relevance_score'] = 100 - ($index * 10);
                return $comp;
            }, $competitors, array_keys($competitors));
        }
    }

    /**
     * Save competitors to database
     */
    public function saveCompetitors(array $competitors): array
    {
        $savedCompetitors = [];

        foreach ($competitors as $competitorData) {
            // Set 'name' field for legacy compatibility (same as business_name)
            $competitorData['name'] = $competitorData['business_name'] ?? 'Unknown';
            
            $competitor = Competitor::create($competitorData);
            $savedCompetitors[] = $competitor;
        }

        return $savedCompetitors;
    }
}
