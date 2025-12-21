<?php

use Illuminate\Support\Facades\Route;
use App\Services\MarketAnalysis\CompetitorAnalysisService;
use Illuminate\Support\Facades\Log;

Route::get('/test-competitor', function () {
    Log::info("=== STARTING COMPETITOR TEST ===");
    
    $service = new CompetitorAnalysisService();
    
    // Test with noon page
    $result = $service->analyzeCompetitor(
        1, // user_id
        'noon', // Facebook page ID or URL
        'Noon Shopping', // competitor name
        '1.1M' // manual follower count
    );
    
    Log::info("=== TEST RESULT ===", $result);
    
    return response()->json([
        'result' => $result,
        'message' => 'Check storage/logs/laravel.log for detailed logs'
    ]);
});
