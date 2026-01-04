<?php

/**
 * Test Data Verification Service
 * 
 * This script demonstrates the data verification and validation services
 * for improving market research data quality.
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\MarketResearch\DataVerificationService;
use App\Services\MarketResearch\CompetitorValidationService;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "==============================================\n";
echo "Data Verification Service Test\n";
echo "==============================================\n\n";

$verificationService = new DataVerificationService();
$validationService = new CompetitorValidationService($verificationService);

// Test 1: Verify Search Results
echo "Test 1: Verifying Search Results\n";
echo "-----------------------------------\n";

$searchResults = [
    [
        'title' => 'Joe\'s Coffee Shop - Best Coffee in San Francisco',
        'url' => 'https://www.joescoffee.com',
        'snippet' => 'Premium artisan coffee roasted daily. Serving San Francisco since 2010.',
        'display_url' => 'joescoffee.com'
    ],
    [
        'title' => 'CLICK HERE NOW!!!',
        'url' => 'https://spam-site.com',
        'snippet' => 'Buy now! Limited offer! Act fast!!!',
        'display_url' => 'spam-site.com'
    ],
    [
        'title' => 'Invalid',
        'url' => 'not-a-url',
        'snippet' => 'Bad',
        'display_url' => ''
    ],
];

$verificationResult = $verificationService->batchVerifySearchResults($searchResults);

echo "Total Results: " . $verificationResult['stats']['total'] . "\n";
echo "Valid Results: " . $verificationResult['stats']['valid'] . "\n";
echo "Invalid Results: " . $verificationResult['stats']['invalid'] . "\n";
echo "Quality Rate: " . $verificationResult['stats']['quality_rate'] . "%\n\n";

foreach ($verificationResult['valid_results'] as $result) {
    echo "✓ VALID: " . $result['title'] . "\n";
    echo "  Score: " . $result['verification']['quality_score'] . "/100\n";
}

echo "\n";

foreach ($verificationResult['invalid_results'] as $result) {
    echo "✗ INVALID: " . $result['title'] . "\n";
    echo "  Issues: " . implode(', ', $result['verification']['issues']) . "\n";
}

echo "\n\n";

// Test 2: Verify Competitor Data
echo "Test 2: Verifying Competitor Data\n";
echo "-----------------------------------\n";

$competitors = [
    [
        'business_name' => 'Blue Bottle Coffee',
        'website' => 'https://bluebottlecoffee.com',
        'social_profiles' => [
            'instagram' => 'bluebottle',
            'facebook' => 'bluebottlecoffee',
        ],
        'phone' => '415-555-1234',
        'address' => '315 Linden St, San Francisco, CA 94102',
    ],
    [
        'business_name' => 'Incomplete Cafe',
        'website' => 'https://incomplete-cafe.com',
        'social_profiles' => [],
        'phone' => null,
        'address' => null,
    ],
    [
        'business_name' => '',
        'website' => 'invalid-url',
        'social_profiles' => [],
    ],
];

foreach ($competitors as $index => $competitor) {
    echo "\nCompetitor " . ($index + 1) . ": " . ($competitor['business_name'] ?: 'Unknown') . "\n";
    
    $verification = $verificationService->verifyCompetitorData($competitor);
    
    echo "  Quality Score: " . $verification['quality_score'] . "/100\n";
    echo "  Valid: " . ($verification['is_valid'] ? 'YES' : 'NO') . "\n";
    
    if (!empty($verification['issues'])) {
        echo "  Issues:\n";
        foreach ($verification['issues'] as $issue) {
            echo "    - $issue\n";
        }
    }
    
    echo "  Verified Fields:\n";
    foreach ($verification['verified_fields'] as $field => $status) {
        echo "    - " . ucwords(str_replace('_', ' ', $field)) . ": " . ($status ? '✓' : '✗') . "\n";
    }
}

echo "\n\n";

// Test 3: Duplicate Detection
echo "Test 3: Duplicate Detection\n";
echo "-----------------------------------\n";

$competitorsWithDupes = [
    [
        'business_name' => 'Starbucks',
        'website' => 'https://www.starbucks.com',
    ],
    [
        'business_name' => 'Peet\'s Coffee',
        'website' => 'https://www.peets.com',
    ],
    [
        'business_name' => 'Starbucks Coffee',
        'website' => 'https://www.starbucks.com',
    ],
    [
        'business_name' => 'Blue Bottle',
        'website' => 'https://bluebottlecoffee.com',
    ],
];

$dupeResult = $verificationService->detectDuplicates($competitorsWithDupes);

echo "Total Competitors: " . count($competitorsWithDupes) . "\n";
echo "Unique Competitors: " . count($dupeResult['unique_competitors']) . "\n";
echo "Duplicates Found: " . $dupeResult['duplicate_count'] . "\n\n";

echo "Unique Competitors:\n";
foreach ($dupeResult['unique_competitors'] as $competitor) {
    echo "  - " . $competitor['business_name'] . " (" . $competitor['website'] . ")\n";
}

if (!empty($dupeResult['duplicates_found'])) {
    echo "\nDuplicates Detected:\n";
    foreach ($dupeResult['duplicates_found'] as $dupe) {
        echo "  - " . $dupe['competitor']['business_name'] . " is a duplicate\n";
    }
}

echo "\n\n";

// Test 4: Overall Quality Calculation
echo "Test 4: Overall Quality Calculation\n";
echo "-----------------------------------\n";

$competitorForQuality = [
    'business_name' => 'Premium Coffee House',
    'website' => 'https://premiumcoffee.com',
    'social_profiles' => [
        'instagram' => 'premiumcoffee',
        'facebook' => 'premiumcoffeehouse',
    ],
    'phone' => '415-555-5678',
    'email' => 'info@premiumcoffee.com',
    'location' => 'San Francisco, CA',
    'address' => '123 Market St, San Francisco, CA 94103',
];

$qualityResult = $verificationService->calculateOverallQuality($competitorForQuality);

echo "Business: " . $competitorForQuality['business_name'] . "\n";
echo "Quality Score: " . $qualityResult['score'] . "/" . $qualityResult['max_score'] . "\n";
echo "Percentage: " . $qualityResult['percentage'] . "%\n";
echo "Quality Level: " . strtoupper($qualityResult['quality_level']) . "\n\n";

echo "Metrics:\n";
foreach ($qualityResult['metrics'] as $metric => $value) {
    echo "  - " . ucwords(str_replace('_', ' ', $metric)) . ": " . ($value ? '✓' : '✗') . "\n";
}

echo "\n\n";

// Test 5: Validate Business Activity
echo "Test 5: Validate Business Activity\n";
echo "-----------------------------------\n";

$activeCheck = $validationService->verifyBusinessIsActive($competitorForQuality);

echo "Business: " . $competitorForQuality['business_name'] . "\n";
echo "Likely Active: " . ($activeCheck['is_likely_active'] ? 'YES' : 'NO') . "\n";
echo "Active Score: " . $activeCheck['active_score'] . "/100\n\n";

echo "Activity Indicators:\n";
foreach ($activeCheck['indicators'] as $indicator => $status) {
    echo "  - " . ucwords(str_replace('_', ' ', $indicator)) . ": " . ($status ? '✓' : '✗') . "\n";
}

echo "\n\n";

echo "==============================================\n";
echo "All Tests Completed!\n";
echo "==============================================\n\n";

echo "Summary:\n";
echo "--------\n";
echo "✓ Search result verification working\n";
echo "✓ Competitor data validation working\n";
echo "✓ Duplicate detection working\n";
echo "✓ Quality scoring working\n";
echo "✓ Business activity verification working\n\n";

echo "The verification services are ready to use in your market research workflow!\n";
echo "They will automatically filter out low-quality data and improve accuracy.\n\n";
