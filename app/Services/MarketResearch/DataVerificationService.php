<?php

namespace App\Services\MarketResearch;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Data Verification Service
 * Validates and verifies search results and scraped data quality
 */
class DataVerificationService
{
    /**
     * Verify search result quality
     */
    public function verifySearchResult(array $result): array
    {
        $qualityScore = 0;
        $issues = [];

        // Check URL validity
        if ($this->isValidUrl($result['url'] ?? '')) {
            $qualityScore += 25;
        } else {
            $issues[] = 'Invalid or missing URL';
        }

        // Check title quality
        if ($this->hasQualityTitle($result['title'] ?? '')) {
            $qualityScore += 20;
        } else {
            $issues[] = 'Poor quality or missing title';
        }

        // Check snippet/description quality
        if ($this->hasQualitySnippet($result['snippet'] ?? '')) {
            $qualityScore += 15;
        } else {
            $issues[] = 'Poor quality or missing description';
        }

        // Verify URL is accessible
        if ($this->isUrlAccessible($result['url'] ?? '')) {
            $qualityScore += 30;
        } else {
            $issues[] = 'URL is not accessible';
        }

        // Check for spam indicators
        if (!$this->hasSpamIndicators($result['title'] ?? '', $result['snippet'] ?? '')) {
            $qualityScore += 10;
        } else {
            $issues[] = 'Contains spam indicators';
        }

        return [
            'is_valid' => $qualityScore >= 60,
            'quality_score' => $qualityScore,
            'issues' => $issues,
            'verified_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Verify competitor data quality
     */
    public function verifyCompetitorData(array $competitorData): array
    {
        $qualityScore = 0;
        $issues = [];
        $verified = [];

        // Validate business name
        if (!empty($competitorData['business_name']) && strlen($competitorData['business_name']) > 2) {
            $qualityScore += 20;
            $verified['business_name'] = true;
        } else {
            $issues[] = 'Missing or invalid business name';
            $verified['business_name'] = false;
        }

        // Validate website URL
        if ($this->isValidUrl($competitorData['website'] ?? '')) {
            $qualityScore += 20;
            $verified['website'] = true;
        } else {
            $issues[] = 'Invalid website URL';
            $verified['website'] = false;
        }

        // Verify social profiles exist
        $socialProfiles = $competitorData['social_profiles'] ?? [];
        $validSocialCount = 0;

        foreach ($socialProfiles as $platform => $handle) {
            if (!empty($handle) && $this->verifySocialProfile($platform, $handle)) {
                $validSocialCount++;
            }
        }

        if ($validSocialCount > 0) {
            $qualityScore += min(30, $validSocialCount * 10);
            $verified['social_profiles'] = true;
        } else {
            $issues[] = 'No valid social profiles found';
            $verified['social_profiles'] = false;
        }

        // Check contact information
        $hasContact = !empty($competitorData['phone']) || !empty($competitorData['email']);
        if ($hasContact) {
            $qualityScore += 15;
            $verified['contact_info'] = true;
        } else {
            $verified['contact_info'] = false;
        }

        // Check location information
        if (!empty($competitorData['address']) || !empty($competitorData['location'])) {
            $qualityScore += 15;
            $verified['location'] = true;
        } else {
            $verified['location'] = false;
        }

        return [
            'is_valid' => $qualityScore >= 50,
            'quality_score' => $qualityScore,
            'issues' => $issues,
            'verified_fields' => $verified,
            'verified_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Check for duplicate competitors
     */
    public function detectDuplicates(array $competitors): array
    {
        $seen = [];
        $duplicates = [];
        $unique = [];

        foreach ($competitors as $index => $competitor) {
            $identifier = $this->generateCompetitorIdentifier($competitor);

            if (isset($seen[$identifier])) {
                $duplicates[] = [
                    'index' => $index,
                    'duplicate_of' => $seen[$identifier],
                    'competitor' => $competitor,
                ];
            } else {
                $seen[$identifier] = $index;
                $unique[] = $competitor;
            }
        }

        Log::info('Duplicate detection complete', [
            'total' => count($competitors),
            'unique' => count($unique),
            'duplicates' => count($duplicates),
        ]);

        return [
            'unique_competitors' => $unique,
            'duplicates_found' => $duplicates,
            'duplicate_count' => count($duplicates),
        ];
    }

    /**
     * Verify social media profile exists
     */
    public function verifySocialProfile(string $platform, string $handle): bool
    {
        try {
            $url = $this->buildSocialProfileUrl($platform, $handle);
            
            if (!$url) {
                return false;
            }

            // Quick HEAD request to check if profile exists
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->head($url);

            return $response->successful();
        } catch (\Exception $e) {
            Log::debug('Social profile verification failed', [
                'platform' => $platform,
                'handle' => $handle,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Batch verify multiple search results
     */
    public function batchVerifySearchResults(array $results): array
    {
        $verified = [];
        $valid = [];
        $invalid = [];

        foreach ($results as $index => $result) {
            $verification = $this->verifySearchResult($result);
            $result['verification'] = $verification;

            $verified[] = $result;

            if ($verification['is_valid']) {
                $valid[] = $result;
            } else {
                $invalid[] = $result;
            }
        }

        return [
            'all_results' => $verified,
            'valid_results' => $valid,
            'invalid_results' => $invalid,
            'stats' => [
                'total' => count($results),
                'valid' => count($valid),
                'invalid' => count($invalid),
                'quality_rate' => count($results) > 0 ? round((count($valid) / count($results)) * 100, 2) : 0,
            ],
        ];
    }

    /**
     * Check if URL is valid
     */
    public function isValidUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Validate URL format
        $validator = Validator::make(['url' => $url], [
            'url' => 'url',
        ]);

        if ($validator->fails()) {
            return false;
        }

        // Check for common invalid patterns
        $invalidPatterns = [
            '/youtube\.com/',
            '/facebook\.com\/pages/',
            '/linkedin\.com\/in/',
            '/twitter\.com\/search/',
            '/instagram\.com\/explore/',
        ];

        foreach ($invalidPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if URL is accessible
     */
    private function isUrlAccessible(string $url): bool
    {
        if (empty($url) || !$this->isValidUrl($url)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; MarketResearchBot/1.0)',
                ])
                ->head($url);

            return $response->successful();
        } catch (\Exception $e) {
            Log::debug('URL accessibility check failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if title has quality content
     */
    private function hasQualityTitle(string $title): bool
    {
        if (empty($title)) {
            return false;
        }

        // Title should be at least 10 characters
        if (strlen($title) < 10) {
            return false;
        }

        // Should not be all caps (spam indicator)
        if ($title === strtoupper($title) && strlen($title) > 15) {
            return false;
        }

        return true;
    }

    /**
     * Check if snippet has quality content
     */
    private function hasQualitySnippet(string $snippet): bool
    {
        if (empty($snippet)) {
            return false;
        }

        // Snippet should have reasonable length
        if (strlen($snippet) < 20) {
            return false;
        }

        return true;
    }

    /**
     * Check for spam indicators
     */
    private function hasSpamIndicators(string $title, string $snippet): bool
    {
        $spamKeywords = [
            'buy now',
            'click here',
            'limited offer',
            'act fast',
            '!!!',
            'free money',
            'make money fast',
        ];

        $text = strtolower($title . ' ' . $snippet);

        foreach ($spamKeywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate unique identifier for competitor
     */
    private function generateCompetitorIdentifier(array $competitor): string
    {
        // Use website domain as primary identifier
        if (!empty($competitor['website'])) {
            $domain = parse_url($competitor['website'], PHP_URL_HOST);
            return strtolower(str_replace('www.', '', $domain ?? ''));
        }

        // Fallback to business name
        if (!empty($competitor['business_name'])) {
            return strtolower(trim($competitor['business_name']));
        }

        // Last resort: use URL
        return strtolower($competitor['url'] ?? '');
    }

    /**
     * Build social profile URL from platform and handle
     */
    private function buildSocialProfileUrl(string $platform, string $handle): ?string
    {
        // Remove @ symbol if present
        $handle = ltrim($handle, '@');

        $platformUrls = [
            'facebook' => "https://www.facebook.com/{$handle}",
            'instagram' => "https://www.instagram.com/{$handle}",
            'twitter' => "https://twitter.com/{$handle}",
            'x' => "https://x.com/{$handle}",
            'linkedin' => $handle, // Already full URL
            'tiktok' => "https://www.tiktok.com/@{$handle}",
        ];

        return $platformUrls[strtolower($platform)] ?? null;
    }

    /**
     * Verify email format
     */
    public function isValidEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Verify phone format
     */
    public function isValidPhone(?string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        // Remove common formatting characters
        $cleaned = preg_replace('/[\s\-\(\)\.]/', '', $phone);

        // Should have 10-15 digits
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15 && ctype_digit($cleaned);
    }

    /**
     * Calculate overall data quality score
     */
    public function calculateOverallQuality(array $competitorData): array
    {
        $metrics = [
            'has_website' => !empty($competitorData['website']) ? 1 : 0,
            'has_business_name' => !empty($competitorData['business_name']) ? 1 : 0,
            'has_social_profiles' => !empty($competitorData['social_profiles']) ? 1 : 0,
            'has_contact_info' => (!empty($competitorData['phone']) || !empty($competitorData['email'])) ? 1 : 0,
            'has_location' => !empty($competitorData['location']) ? 1 : 0,
        ];

        $score = array_sum($metrics);
        $maxScore = count($metrics);

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 2),
            'metrics' => $metrics,
            'quality_level' => $this->getQualityLevel($score, $maxScore),
        ];
    }

    /**
     * Get quality level based on score
     */
    private function getQualityLevel(int $score, int $maxScore): string
    {
        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 80) return 'excellent';
        if ($percentage >= 60) return 'good';
        if ($percentage >= 40) return 'fair';
        return 'poor';
    }
}
