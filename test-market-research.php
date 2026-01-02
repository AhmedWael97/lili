<?php

// Quick test script for Market Research API
// Run: php test-market-research.php

$apiUrl = 'http://localhost:8000/api/market-research';

$data = [
    'business_idea' => 'Organic coffee shop with local pastries',
    'location' => 'Austin, TX'
];

echo "🔬 Testing Market Research API\n";
echo "================================\n\n";
echo "Business Idea: {$data['business_idea']}\n";
echo "Location: {$data['location']}\n\n";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

echo "📤 Sending request...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "📨 Response (HTTP {$httpCode}):\n";
echo "================================\n";

if ($response) {
    $decoded = json_decode($response, true);
    echo json_encode($decoded, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($decoded['data']['request_id'])) {
        $requestId = $decoded['data']['request_id'];
        echo "✅ Research request submitted successfully!\n";
        echo "Request ID: {$requestId}\n\n";
        
        echo "To check status, run:\n";
        echo "  curl http://localhost:8000/api/market-research/{$requestId}/status\n\n";
        
        echo "To get report when complete:\n";
        echo "  curl http://localhost:8000/api/market-research/{$requestId}/report\n\n";
        
        echo "💡 Processing takes 2-3 minutes. Check the queue worker logs.\n";
    }
} else {
    echo "❌ No response received\n";
}
