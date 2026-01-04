<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🚀 Testing Multi-Source Data Collection System\n";
echo str_repeat("=", 60) . "\n\n";

$scraper = app(\App\Services\MarketResearch\SocialMediaScraperService::class);

// Test Facebook with multiple sources
echo "📘 Test 1: Facebook Multi-Source Scraping\n";
echo str_repeat("-", 60) . "\n";
$fbResult = $scraper->scrapeFacebookPage('nike');

echo "✅ Success: " . ($fbResult['success'] ? 'YES' : 'NO') . "\n";
echo "📊 Source: " . ($fbResult['source'] ?? 'N/A') . "\n";
echo "👥 Followers: " . number_format($fbResult['followers'] ?? 0) . "\n";

if (isset($fbResult['sources_used'])) {
    echo "🔄 Sources Used: " . $fbResult['sources_used'] . "\n";
    echo "📈 Data Variance: " . ($fbResult['variance'] ?? 0) . "%\n";
    echo "✨ Data Quality: " . ($fbResult['data_quality'] ?? 'N/A') . "\n";
}

if (isset($fbResult['note'])) {
    echo "ℹ️  Note: " . $fbResult['note'] . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Test Instagram with multiple sources
echo "📷 Test 2: Instagram Multi-Source Scraping\n";
echo str_repeat("-", 60) . "\n";
$igResult = $scraper->scrapeInstagramProfile('nike');

echo "✅ Success: " . ($igResult['success'] ? 'YES' : 'NO') . "\n";
echo "📊 Source: " . ($igResult['source'] ?? 'N/A') . "\n";
echo "👥 Followers: " . number_format($igResult['followers'] ?? 0) . "\n";

if (isset($igResult['sources_used'])) {
    echo "🔄 Sources Used: " . $igResult['sources_used'] . "\n";
    echo "📈 Data Variance: " . ($igResult['variance'] ?? 0) . "%\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Test Google Maps integration
echo "🗺️  Test 3: Google Maps Business Data\n";
echo str_repeat("-", 60) . "\n";
$mapsResult = $scraper->scrapeGoogleMapsData('Starbucks Coffee', 'Miami Florida');

if ($mapsResult && $mapsResult['success']) {
    echo "✅ Success: YES\n";
    echo "📊 Source: " . $mapsResult['source'] . "\n";
    echo "🏪 Name: " . ($mapsResult['name'] ?? 'N/A') . "\n";
    echo "📍 Address: " . ($mapsResult['address'] ?? 'N/A') . "\n";
    echo "📞 Phone: " . ($mapsResult['phone'] ?? 'N/A') . "\n";
    echo "🌐 Website: " . ($mapsResult['website'] ?? 'N/A') . "\n";
    echo "⭐ Rating: " . ($mapsResult['rating'] ?? 'N/A') . "\n";
    echo "💬 Reviews: " . ($mapsResult['reviews_count'] ?? 0) . "\n";
} else {
    echo "❌ Google Maps data not available\n";
    echo "ℹ️  Configure GOOGLE_API_KEY in .env for this feature\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Summary
echo "📊 SUMMARY: Multi-Source Data Collection\n";
echo str_repeat("=", 60) . "\n\n";

$sources = [];
if (strpos($fbResult['source'], 'cross_validated') !== false) {
    $sources[] = "✅ Facebook: Cross-validated from multiple sources";
} elseif ($fbResult['source'] === 'social_blade_api') {
    $sources[] = "✅ Facebook: Social Blade API";
} elseif ($fbResult['source'] === 'facebook_public_scraping') {
    $sources[] = "⚠️  Facebook: Public scraping (limited)";
} else {
    $sources[] = "❌ Facebook: Estimated data";
}

if (strpos($igResult['source'], 'cross_validated') !== false) {
    $sources[] = "✅ Instagram: Cross-validated from multiple sources";
} elseif ($igResult['source'] === 'social_blade_api') {
    $sources[] = "✅ Instagram: Social Blade API";
} elseif ($igResult['source'] === 'instagram_public_api') {
    $sources[] = "✅ Instagram: Public API";
} else {
    $sources[] = "❌ Instagram: Estimated data";
}

if ($mapsResult && $mapsResult['success']) {
    $sources[] = "✅ Google Maps: Business data enrichment";
} else {
    $sources[] = "⚠️  Google Maps: Not configured";
}

foreach ($sources as $source) {
    echo $source . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";

echo "💡 Data Quality Estimate:\n";
$realDataCount = 0;
if ($fbResult['success'] && $fbResult['source'] !== 'estimated') $realDataCount++;
if ($igResult['success'] && $igResult['source'] !== 'estimated') $realDataCount++;
if ($mapsResult && $mapsResult['success']) $realDataCount++;

$percentage = round(($realDataCount / 3) * 100);
echo "   Real Data: {$realDataCount}/3 sources ({$percentage}%)\n";

if ($percentage >= 70) {
    echo "   Quality: ⭐⭐⭐⭐⭐ EXCELLENT\n";
} elseif ($percentage >= 50) {
    echo "   Quality: ⭐⭐⭐⭐ GOOD\n";
} elseif ($percentage >= 30) {
    echo "   Quality: ⭐⭐⭐ FAIR\n";
} else {
    echo "   Quality: ⭐⭐ POOR - Consider configuring APIs\n";
}

echo "\n";
