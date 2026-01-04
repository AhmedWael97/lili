<?php

use App\Http\Controllers\Api\ContentApiController;
use App\Http\Controllers\Api\AgentApiController;
use App\Http\Controllers\Api\MarketResearchController;
use App\Http\Controllers\Api\FeedbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Market Research API (public for beta - no auth required)
Route::prefix('market-research')->group(function () {
    Route::post('/', [MarketResearchController::class, 'submitResearch']);
    Route::get('/{id}/status', [MarketResearchController::class, 'getStatus']);
    Route::get('/{id}/report', [MarketResearchController::class, 'getReport']);
    Route::get('/requests', [MarketResearchController::class, 'listRequests']);
});

// Feedback API (public for beta testing - add auth later)
Route::prefix('feedback')->group(function () {
    // Submit feedback
    Route::post('/competitor', [FeedbackController::class, 'submitCompetitorFeedback']);
    Route::post('/batch', [FeedbackController::class, 'batchSubmitFeedback']);
    
    // Get feedback stats
    Route::get('/competitor/{competitorId}', [FeedbackController::class, 'getCompetitorFeedback']);
    Route::get('/research-request/{researchRequestId}', [FeedbackController::class, 'getResearchRequestFeedback']);
    Route::get('/export/{researchRequestId}', [FeedbackController::class, 'exportFeedback']);
    
    // Learning & Performance
    Route::get('/performance', [FeedbackController::class, 'getPerformanceDashboard']);
    Route::get('/thresholds', [FeedbackController::class, 'getThresholds']);
    Route::post('/learn', [FeedbackController::class, 'triggerLearning']);
    Route::post('/train', [FeedbackController::class, 'trainModel']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Content API
    Route::prefix('content')->group(function () {
        Route::get('/', [ContentApiController::class, 'index']);
        Route::post('/', [ContentApiController::class, 'store']);
        Route::get('/statistics', [ContentApiController::class, 'statistics']);
        Route::get('/{id}', [ContentApiController::class, 'show']);
        Route::put('/{id}', [ContentApiController::class, 'update']);
        Route::delete('/{id}', [ContentApiController::class, 'destroy']);
    });

    // AI Agents API
    Route::prefix('agents')->group(function () {
        Route::post('/strategy', [AgentApiController::class, 'strategy']);
        Route::post('/caption', [AgentApiController::class, 'caption']);
        Route::post('/image-prompt', [AgentApiController::class, 'imagePrompt']);
        Route::post('/image', [AgentApiController::class, 'image']);
        Route::post('/comment-reply', [AgentApiController::class, 'commentReply']);
        Route::post('/ad-campaign', [AgentApiController::class, 'adCampaign']);
    });

    // User info
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });
});
