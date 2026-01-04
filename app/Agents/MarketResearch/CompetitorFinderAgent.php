<?php

namespace App\Agents\MarketResearch;

use App\Models\Competitor;
use App\Models\ResearchRequest;
use App\Services\MarketResearch\GoogleSearchService;
use App\Services\MarketResearch\WebScraperService;
use App\Services\MarketResearch\DataVerificationService;
use App\Services\MarketResearch\CompetitorValidationService;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class CompetitorFinderAgent
{
    private GoogleSearchService $googleSearch;
    private WebScraperService $webScraper;
    private DataVerificationService $verificationService;
    private CompetitorValidationService $validationService;

    public function __construct(
        GoogleSearchService $googleSearch,
        WebScraperService $webScraper,
        DataVerificationService $verificationService,
        CompetitorValidationService $validationService
    ) {
        $this->googleSearch = $googleSearch;
        $this->webScraper = $webScraper;
        $this->verificationService = $verificationService;
        $this->validationService = $validationService;
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

        // Step 2.5: Verify search results quality
        $verifiedResults = $this->verificationService->batchVerifySearchResults($searchResults);
        $validSearchResults = array_column($verifiedResults, 'result');
        
        $competitors = [];
        
        // Step 3: Process each valid search result
        foreach ($validSearchResults as $result) {
            $competitor = $this->processSearchResult($result, $request->id);
            
            if ($competitor) {
                $competitors[] = $competitor;
            }

            // Respect rate limits
            sleep(1);
        }

        // Step 3.5: Remove duplicates
        $deduplicationResult = $this->verificationService->detectDuplicates($competitors);
        $uniqueCompetitors = $deduplicationResult['unique_competitors'];

        Log::info('Duplicates removed', [
            'original_count' => count($competitors),
            'unique_count' => count($uniqueCompetitors),
            'duplicates_removed' => $deduplicationResult['duplicate_count']
        ]);

        // Step 4: AI Pre-Filter - Intelligently screen data before user verification
        $preFilterResult = $this->validationService->intelligentPreFilter(
            $uniqueCompetitors,
            $request->business_idea,
            $request->location
        );

        Log::info('AI pre-filter completed', [
            'auto_approved' => $preFilterResult['stats']['auto_approved'],
            'needs_verification' => $preFilterResult['stats']['needs_manual_review'],
            'auto_rejected' => $preFilterResult['stats']['auto_rejected'],
            'workload_reduction' => $preFilterResult['stats']['user_workload_reduction']
        ]);

        // Combine auto-approved with those needing verification
        $competitorsForUser = array_merge(
            $preFilterResult['auto_approved'],
            $preFilterResult['needs_verification']
        );

        // Step 5: Rank competitors by relevance
        $rankedCompetitors = $this->rankCompetitors($competitorsForUser, $request->business_idea);

        // Step 6: Sort by quality and relevance
        $finalCompetitors = $this->validationService->sortByQuality($rankedCompetitors, 'desc');

        Log::info('Competitor search completed', [
            'request_id' => $request->id,
            'initial_results' => count($searchResults),
            'after_verification' => count($validSearchResults),
            'after_deduplication' => count($uniqueCompetitors),
            'after_ai_filter' => count($competitorsForUser),
            'auto_rejected_by_ai' => count($preFilterResult['auto_rejected']),
            'final_count' => count($finalCompetitors)
        ]);

        return $finalCompetitors;
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
     * Process a single search result with multi-source data enrichment
     */
    private function processSearchResult(array $result, int $requestId): ?array
    {
        try {
            $url = $result['url'];
            $businessName = $this->cleanBusinessName($result['title']);

            // Extract social profiles from website
            $socialProfiles = $this->webScraper->extractSocialProfiles($url);

            // Extract business information from website
            $businessInfo = $this->webScraper->extractBusinessInfo($url);

            // Try to enrich with Google Maps data if available
            $scraper = app(\App\Services\MarketResearch\SocialMediaScraperService::class);
            $request = \App\Models\ResearchRequest::find($requestId);
            
            if ($request) {
                $googleMapsData = $scraper->scrapeGoogleMapsData($businessName, $request->location);
                
                if ($googleMapsData && $googleMapsData['success']) {
                    // Enrich with Google Maps data
                    $businessInfo['phone'] = $businessInfo['phone'] ?? $googleMapsData['phone'];
                    $businessInfo['address'] = $businessInfo['address'] ?? $googleMapsData['address'];
                    $businessInfo['rating'] = $googleMapsData['rating'];
                    $businessInfo['reviews_count'] = $googleMapsData['reviews_count'];
                    
                    // If website not found, use Google Maps website
                    if (!$url || strpos($url, 'google.com') !== false) {
                        $url = $googleMapsData['website'] ?? $url;
                    }
                }
            }

            return [
                'research_request_id' => $requestId,
                'business_name' => $businessName,
                'website' => $url,
                'facebook_handle' => $socialProfiles['facebook'],
                'instagram_handle' => $socialProfiles['instagram'],
                'twitter_handle' => $socialProfiles['twitter'],
                'linkedin_url' => $socialProfiles['linkedin'],
                'phone' => $businessInfo['phone'] ?? null,
                'address' => $businessInfo['address'] ?? null,
                'category' => $googleMapsData['category'] ?? null,
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
