<?php

namespace App\Services\MarketResearch;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Competitor Validation Service
 * Validates competitor relevance and business information using AI
 */
class CompetitorValidationService
{
    private DataVerificationService $verificationService;
    private ?LearningService $learningService = null;

    public function __construct(DataVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
        
        // Lazy load learning service to avoid circular dependencies
        try {
            $this->learningService = app(LearningService::class);
        } catch (\Exception $e) {
            Log::warning('LearningService not available', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Validate competitor relevance to business idea
     */
    public function validateRelevance(array $competitor, string $businessIdea, string $location): array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business analyst that validates competitor relevance. Analyze if a business is a true competitor to the given business idea. Respond with JSON only.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->buildRelevancePrompt($competitor, $businessIdea, $location)
                    ]
                ],
                'temperature' => 0.3,
                'response_format' => ['type' => 'json_object'],
            ]);

            $analysis = json_decode($response->choices[0]->message->content, true);

            return [
                'is_relevant' => $analysis['is_relevant'] ?? false,
                'relevance_score' => $analysis['relevance_score'] ?? 0,
                'reason' => $analysis['reason'] ?? 'Unknown',
                'category_match' => $analysis['category_match'] ?? false,
                'location_match' => $analysis['location_match'] ?? false,
                'target_audience_match' => $analysis['target_audience_match'] ?? false,
            ];
        } catch (\Exception $e) {
            Log::error('Relevance validation failed', [
                'competitor' => $competitor['business_name'] ?? 'Unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'is_relevant' => true, // Default to true on error to avoid false negatives
                'relevance_score' => 50,
                'reason' => 'Validation failed, included by default',
                'category_match' => false,
                'location_match' => false,
                'target_audience_match' => false,
            ];
        }
    }

    /**
     * Validate complete competitor data
     */
    public function validateCompetitor(array $competitor, string $businessIdea, string $location): array
    {
        $validations = [];

        // 1. Data quality verification
        $qualityCheck = $this->verificationService->verifyCompetitorData($competitor);
        $validations['data_quality'] = $qualityCheck;

        // 2. Relevance validation
        $relevanceCheck = $this->validateRelevance($competitor, $businessIdea, $location);
        $validations['relevance'] = $relevanceCheck;

        // 3. Business information completeness
        $completenessCheck = $this->checkCompleteness($competitor);
        $validations['completeness'] = $completenessCheck;

        // Calculate overall validation score
        $overallScore = $this->calculateValidationScore($validations);

        return [
            'is_valid' => $overallScore >= 60,
            'overall_score' => $overallScore,
            'validations' => $validations,
            'recommendation' => $this->getRecommendation($overallScore),
            'validated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * AI Pre-Filter: Intelligently screen data before user verification
     * This filters out obvious spam, irrelevant results, and low-quality data
     * Only high-confidence candidates are sent to users for manual verification
     */
    public function intelligentPreFilter(array $competitors, string $businessIdea, string $location): array
    {
        Log::info('Starting AI pre-filter', [
            'total_competitors' => count($competitors),
            'business_idea' => $businessIdea
        ]);

        $autoApproved = [];
        $needsVerification = [];
        $autoRejected = [];

        foreach ($competitors as $competitor) {
            $filterResult = $this->aiDeepScan($competitor, $businessIdea, $location);
            
            $competitor['ai_filter'] = $filterResult;

            // Auto-approve: High confidence + relevant + quality
            if ($filterResult['confidence'] >= 90 && 
                $filterResult['is_relevant'] && 
                !$filterResult['is_spam'] && 
                $filterResult['quality_score'] >= 75) {
                
                $autoApproved[] = $competitor;
                Log::debug('Auto-approved competitor', [
                    'name' => $competitor['business_name'],
                    'confidence' => $filterResult['confidence']
                ]);
            }
            // Auto-reject: High confidence spam/irrelevant/low quality
            elseif (($filterResult['confidence'] >= 85 && !$filterResult['is_relevant']) ||
                    $filterResult['is_spam'] ||
                    $filterResult['is_duplicate'] ||
                    $filterResult['quality_score'] < 30) {
                
                $autoRejected[] = $competitor;
                Log::debug('Auto-rejected competitor', [
                    'name' => $competitor['business_name'],
                    'reason' => $filterResult['rejection_reason']
                ]);
            }
            // Uncertain: Needs human verification
            else {
                $needsVerification[] = $competitor;
            }

            usleep(500000); // Rate limiting
        }

        Log::info('AI pre-filter complete', [
            'auto_approved' => count($autoApproved),
            'needs_verification' => count($needsVerification),
            'auto_rejected' => count($autoRejected),
            'reduction_rate' => round((count($autoRejected) / count($competitors)) * 100, 2) . '%'
        ]);

        return [
            'auto_approved' => $autoApproved,
            'needs_verification' => $needsVerification,
            'auto_rejected' => $autoRejected,
            'stats' => [
                'total_input' => count($competitors),
                'auto_approved' => count($autoApproved),
                'needs_manual_review' => count($needsVerification),
                'auto_rejected' => count($autoRejected),
                'user_workload_reduction' => round((1 - (count($needsVerification) / count($competitors))) * 100, 2) . '%'
            ]
        ];
    }

    /**
     * Deep AI scan for a single competitor
     * Returns detailed analysis with confidence scores
     */
    private function aiDeepScan(array $competitor, string $businessIdea, string $location): array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert business analyst and data quality specialist. Analyze competitor data with high accuracy and provide confidence scores. Be strict with spam detection and relevance validation. Respond with JSON only.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->buildDeepScanPrompt($competitor, $businessIdea, $location)
                    ]
                ],
                'temperature' => 0.2, // Lower temperature for more consistent judgments
                'response_format' => ['type' => 'json_object'],
            ]);

            $analysis = json_decode($response->choices[0]->message->content, true);

            return [
                'confidence' => $analysis['confidence'] ?? 50,
                'is_relevant' => $analysis['is_relevant'] ?? false,
                'is_spam' => $analysis['is_spam'] ?? false,
                'is_duplicate' => $analysis['is_duplicate'] ?? false,
                'quality_score' => $analysis['quality_score'] ?? 50,
                'relevance_score' => $analysis['relevance_score'] ?? 50,
                'business_type_match' => $analysis['business_type_match'] ?? false,
                'target_market_match' => $analysis['target_market_match'] ?? false,
                'spam_indicators' => $analysis['spam_indicators'] ?? [],
                'quality_issues' => $analysis['quality_issues'] ?? [],
                'rejection_reason' => $analysis['rejection_reason'] ?? null,
                'recommendation' => $analysis['recommendation'] ?? 'review',
                'ai_notes' => $analysis['notes'] ?? ''
            ];
        } catch (\Exception $e) {
            Log::error('AI deep scan failed', [
                'competitor' => $competitor['business_name'] ?? 'Unknown',
                'error' => $e->getMessage()
            ]);

            // Conservative fallback: send to manual review on error
            return [
                'confidence' => 50,
                'is_relevant' => true,
                'is_spam' => false,
                'is_duplicate' => false,
                'quality_score' => 50,
                'relevance_score' => 50,
                'business_type_match' => false,
                'target_market_match' => false,
                'spam_indicators' => [],
                'quality_issues' => ['AI analysis unavailable'],
                'rejection_reason' => null,
                'recommendation' => 'review',
                'ai_notes' => 'Error during AI analysis, requires manual review'
            ];
        }
    }

    /**
     * Build comprehensive prompt for AI deep scan
     */
    private function buildDeepScanPrompt(array $competitor, string $businessIdea, string $location): string
    {
        $businessName = $competitor['business_name'] ?? 'Unknown';
        $website = $competitor['website'] ?? 'N/A';
        $description = $competitor['snippet'] ?? $competitor['description'] ?? 'N/A';
        $phone = $competitor['phone'] ?? 'N/A';
        $address = $competitor['address'] ?? 'N/A';
        $category = $competitor['category'] ?? 'N/A';

        return <<<PROMPT
Perform a comprehensive analysis of this potential competitor data.

TARGET BUSINESS:
- Business Idea: {$businessIdea}
- Target Location: {$location}

COMPETITOR TO ANALYZE:
- Business Name: {$businessName}
- Website: {$website}
- Description: {$description}
- Phone: {$phone}
- Address: {$address}
- Category: {$category}

ANALYSIS REQUIREMENTS:

1. RELEVANCE CHECK (0-100):
   - Is this a direct or indirect competitor?
   - Do they target the same customer base?
   - Similar products/services/industry?
   - Geographic relevance?

2. SPAM DETECTION:
   Check for spam indicators:
   - Generic/suspicious business names
   - Missing critical information
   - Nonsensical descriptions
   - URL patterns typical of spam
   - Irrelevant listings (job boards, directories, etc.)
   - News articles or blog posts (not actual businesses)
   - Review sites or aggregators

3. QUALITY ASSESSMENT (0-100):
   - Completeness of business information
   - Professionalism of data
   - Legitimacy indicators
   - Data consistency

4. DUPLICATE DETECTION:
   - Could this be a duplicate of typical entries?
   - Generic placeholder data?

5. BUSINESS TYPE MATCH:
   - Same industry vertical?
   - Comparable business model?
   - Similar scale/target market?

CONFIDENCE SCORE: How confident are you in your analysis? (0-100)
- 90-100: Extremely confident, clear case
- 70-89: High confidence, strong indicators
- 50-69: Moderate confidence, some uncertainty
- Below 50: Low confidence, borderline case

Respond in JSON format:
{
  "confidence": 0-100,
  "is_relevant": true/false,
  "is_spam": true/false,
  "is_duplicate": true/false,
  "quality_score": 0-100,
  "relevance_score": 0-100,
  "business_type_match": true/false,
  "target_market_match": true/false,
  "spam_indicators": ["list", "of", "issues"],
  "quality_issues": ["list", "of", "problems"],
  "rejection_reason": "Brief reason if should be rejected, or null",
  "recommendation": "approve|review|reject",
  "notes": "Brief explanation of your analysis"
}
PROMPT;
    }

    /**
     * Batch validate multiple competitors
     */
    public function batchValidateCompetitors(array $competitors, string $businessIdea, string $location): array
    {
        $validated = [];
        $approved = [];
        $rejected = [];

        foreach ($competitors as $competitor) {
            $validation = $this->validateCompetitor($competitor, $businessIdea, $location);
            
            $competitor['validation'] = $validation;
            $validated[] = $competitor;

            if ($validation['is_valid'] && $validation['validations']['relevance']['is_relevant']) {
                $approved[] = $competitor;
            } else {
                $rejected[] = $competitor;
            }

            // Rate limiting for AI calls
            usleep(500000); // 0.5 second delay
        }

        Log::info('Batch validation complete', [
            'total' => count($competitors),
            'approved' => count($approved),
            'rejected' => count($rejected),
        ]);

        return [
            'all_competitors' => $validated,
            'approved_competitors' => $approved,
            'rejected_competitors' => $rejected,
            'stats' => [
                'total' => count($competitors),
                'approved' => count($approved),
                'rejected' => count($rejected),
                'approval_rate' => count($competitors) > 0 ? round((count($approved) / count($competitors)) * 100, 2) : 0,
            ],
        ];
    }

    /**
     * Check data completeness
     */
    private function checkCompleteness(array $competitor): array
    {
        $requiredFields = [
            'business_name' => !empty($competitor['business_name']),
            'website' => !empty($competitor['website']),
        ];

        $optionalFields = [
            'social_profiles' => !empty($competitor['social_profiles']),
            'phone' => !empty($competitor['phone']),
            'email' => !empty($competitor['email']),
            'address' => !empty($competitor['address']),
            'description' => !empty($competitor['description']) || !empty($competitor['snippet']),
        ];

        $requiredComplete = array_sum($requiredFields);
        $optionalComplete = array_sum($optionalFields);

        $totalFields = count($requiredFields) + count($optionalFields);
        $completeFields = $requiredComplete + $optionalComplete;

        return [
            'required_fields' => $requiredFields,
            'optional_fields' => $optionalFields,
            'completeness_score' => round(($completeFields / $totalFields) * 100, 2),
            'has_required_fields' => $requiredComplete === count($requiredFields),
            'total_complete' => $completeFields,
            'total_fields' => $totalFields,
        ];
    }

    /**
     * Calculate overall validation score
     */
    private function calculateValidationScore(array $validations): int
    {
        // Get adjusted thresholds from learning service if available
        $thresholds = $this->learningService ? $this->learningService->getAdjustedThresholds() : [];
        
        $weights = [
            'data_quality' => 0.3,
            'relevance' => 0.5,
            'completeness' => 0.2,
        ];

        $score = 0;

        // Data quality score
        if (isset($validations['data_quality']['quality_score'])) {
            $score += $validations['data_quality']['quality_score'] * $weights['data_quality'];
        }

        // Relevance score
        if (isset($validations['relevance']['relevance_score'])) {
            $score += $validations['relevance']['relevance_score'] * $weights['relevance'];
        }

        // Completeness score
        if (isset($validations['completeness']['completeness_score'])) {
            $score += $validations['completeness']['completeness_score'] * $weights['completeness'];
        }

        return (int) round($score);
    }

    /**
     * Get recommendation based on validation score
     */
    private function getRecommendation(int $score): string
    {
        if ($score >= 80) {
            return 'Excellent competitor - include with high confidence';
        } elseif ($score >= 60) {
            return 'Good competitor - include in results';
        } elseif ($score >= 40) {
            return 'Fair competitor - consider including with caveats';
        } else {
            return 'Poor quality - recommend excluding from results';
        }
    }

    /**
     * Build relevance validation prompt
     */
    private function buildRelevancePrompt(array $competitor, string $businessIdea, string $location): string
    {
        $businessName = $competitor['business_name'] ?? 'Unknown';
        $website = $competitor['website'] ?? '';
        $description = $competitor['snippet'] ?? $competitor['description'] ?? '';

        return <<<PROMPT
Analyze if this business is a relevant competitor for the given business idea.

TARGET BUSINESS:
- Business Idea: {$businessIdea}
- Location: {$location}

POTENTIAL COMPETITOR:
- Business Name: {$businessName}
- Website: {$website}
- Description: {$description}

Evaluate:
1. Is this a direct or indirect competitor?
2. Do they serve similar customers?
3. Do they offer similar products/services?
4. Are they in the same geographic market or comparable?

Respond in JSON format with:
{
  "is_relevant": true/false,
  "relevance_score": 0-100,
  "reason": "Brief explanation why relevant or not",
  "category_match": true/false,
  "location_match": true/false,
  "target_audience_match": true/false
}
PROMPT;
    }

    /**
     * Verify business is active and operational
     */
    public function verifyBusinessIsActive(array $competitor): array
    {
        $indicators = [];
        $activeScore = 0;

        // Check if website is accessible
        if (!empty($competitor['website'])) {
            if ($this->verificationService->isValidUrl($competitor['website'])) {
                $activeScore += 30;
                $indicators['website_accessible'] = true;
            } else {
                $indicators['website_accessible'] = false;
            }
        }

        // Check if has recent social media activity (if available)
        if (!empty($competitor['social_profiles'])) {
            $activeScore += 20;
            $indicators['has_social_presence'] = true;
        } else {
            $indicators['has_social_presence'] = false;
        }

        // Check if has contact information
        if (!empty($competitor['phone']) || !empty($competitor['email'])) {
            $activeScore += 25;
            $indicators['has_contact_info'] = true;
        } else {
            $indicators['has_contact_info'] = false;
        }

        // Check if has complete business information
        if (!empty($competitor['address'])) {
            $activeScore += 25;
            $indicators['has_address'] = true;
        } else {
            $indicators['has_address'] = false;
        }

        return [
            'is_likely_active' => $activeScore >= 50,
            'active_score' => $activeScore,
            'indicators' => $indicators,
        ];
    }

    /**?int $minScore = null): array
    {
        // Use adjusted threshold from learning service if available
        if ($minScore === null && $this->learningService) {
            $thresholds = $this->learningService->getAdjustedThresholds();
            $minScore = $thresholds['competitor_quality_min'] ?? 60;
        } elseif ($minScore === null) {
            $minScore = 60; // Default fallback
        }
* Filter competitors by minimum quality threshold
     */
    public function filterByQualityThreshold(array $competitors, int $minScore = 60): array
    {
        return array_filter($competitors, function($competitor) use ($minScore) {
            $validation = $competitor['validation'] ?? null;
            
            if (!$validation) {
                return false;
            }

            return $validation['overall_score'] >= $minScore;
        });
    }

    /**
     * Sort competitors by validation score
     */
    public function sortByQuality(array $competitors, string $order = 'desc'): array
    {
        usort($competitors, function($a, $b) use ($order) {
            $scoreA = $a['validation']['overall_score'] ?? 0;
            $scoreB = $b['validation']['overall_score'] ?? 0;

            return $order === 'desc' ? $scoreB - $scoreA : $scoreA - $scoreB;
        });

        return $competitors;
    }
}
