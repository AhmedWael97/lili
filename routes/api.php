<?php

use App\Http\Controllers\Api\ContentApiController;
use App\Http\Controllers\Api\AgentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

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
