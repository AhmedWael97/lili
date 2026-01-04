<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🧪 Testing Facebook Scraper with Your API Credentials\n";
echo "=====================================================\n\n";

$scraper = app(\App\Services\MarketResearch\SocialMediaScraperService::class);

// Test 1: Nike (popular page)
echo "Test 1: Scraping Nike's official Facebook page...\n";
$result = $scraper->scrapeFacebookPage('nike');

echo "✅ Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "📊 Source: " . ($result['source'] ?? 'N/A') . "\n";
echo "👥 Followers: " . ($result['followers'] ?? 'N/A') . "\n";
echo "📝 Posts Count: " . ($result['posts_count'] ?? 0) . "\n";

if (isset($result['posts']) && count($result['posts']) > 0) {
    echo "\n📱 Sample Post from Official Page:\n";
    $firstPost = $result['posts'][0];
    echo "   Text: " . substr($firstPost['text'] ?? $firstPost['message'] ?? 'No text', 0, 100) . "...\n";
    echo "   Likes: " . ($firstPost['likes'] ?? 0) . "\n";
    echo "   Comments: " . ($firstPost['comments'] ?? 0) . "\n";
}

if (isset($result['warning'])) {
    echo "\n⚠️  Warning: " . $result['warning'] . "\n";
}

if (isset($result['note'])) {
    echo "ℹ️  Note: " . $result['note'] . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test 2: Check if using real API or estimates
if ($result['source'] === 'facebook_graph_api') {
    echo "\n✅ SUCCESS! Facebook Graph API is working!\n";
    echo "   You're now getting OFFICIAL page posts (not customer tags)\n";
    echo "   Data Quality: ⭐⭐⭐⭐⭐ EXCELLENT\n";
} elseif ($result['source'] === 'facebook_public_scraping') {
    echo "\n⚠️  Using public scraping (API fallback)\n";
    echo "   Data Quality: ⭐⭐⭐ GOOD\n";
    echo "   Recommendation: Check Facebook API token\n";
} else {
    echo "\n❌ Using estimates (no real scraping)\n";
    echo "   Data Quality: ⭐ POOR\n";
    echo "   Action Required: Configure Facebook API\n";
}

echo "\n";
