<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FacebookOAuthController;
use App\Http\Controllers\ContentGenerationController;
use App\Http\Controllers\BrandSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AIStudioController;
use App\Http\Controllers\AgentController;
use App\Models\Package;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    $packages = Package::where('is_active', true)
        ->orderBy('price', 'asc')
        ->get();
    return view('welcome', compact('packages'));
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])->name('verify.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Facebook OAuth routes
Route::middleware('auth')->prefix('auth/facebook')->name('facebook.')->group(function () {
    Route::get('/redirect', [FacebookOAuthController::class, 'redirectToFacebook'])->name('redirect');
    Route::get('/callback', [FacebookOAuthController::class, 'handleFacebookCallback'])->name('callback');
    Route::get('/select-pages', [FacebookOAuthController::class, 'selectPages'])->name('select-pages');
    Route::post('/connect-pages', [FacebookOAuthController::class, 'connectPages'])->name('connect-pages');
    Route::post('/disconnect', [FacebookOAuthController::class, 'disconnect'])->name('disconnect');
});

// User dashboard routes
Route::middleware(['auth', 'subscription.active'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/content', [DashboardController::class, 'content'])->name('content');
    Route::get('/agents', [DashboardController::class, 'agents'])->name('agents');
    Route::get('/platforms', [DashboardController::class, 'platforms'])->name('platforms');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [BrandSettingsController::class, 'update'])->name('settings.update');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
});

// Content generation routes
Route::middleware(['auth', 'subscription.active'])->prefix('content')->name('content.')->group(function () {
    Route::get('/create', [ContentGenerationController::class, 'create'])->name('create');
    Route::post('/generate', [ContentGenerationController::class, 'generate'])->name('generate');
    Route::post('/{id}/schedule', [ContentGenerationController::class, 'schedule'])->name('schedule');
    Route::post('/{id}/publish', [ContentGenerationController::class, 'publish'])->name('publish');
});

// AI Studio routes (no Facebook required)
Route::middleware(['auth', 'subscription.active'])->prefix('ai-studio')->name('ai-studio.')->group(function () {
    Route::get('/', [AIStudioController::class, 'index'])->name('index');
    Route::get('/strategy', [AIStudioController::class, 'strategyForm'])->name('strategy');
    Route::post('/strategy/generate', [AIStudioController::class, 'generateStrategy'])->name('generate-strategy');
    Route::post('/strategy/generate-all', [AIStudioController::class, 'generateAllContent'])->name('generate-all-content');
    Route::get('/content', [AIStudioController::class, 'contentForm'])->name('content');
    Route::post('/content/generate', [AIStudioController::class, 'generateContent'])->name('generate-content');
    Route::post('/content/save-draft', [AIStudioController::class, 'saveDraft'])->name('save-draft');
});

// Agent Management routes
Route::middleware(['auth', 'subscription.active'])->prefix('agents')->name('agents.')->group(function () {
    Route::get('/', [AgentController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])->name('dashboard');
    Route::post('/{agentCode}/activate', [AgentController::class, 'activate'])->name('activate');
    Route::delete('/{agentCode}/deactivate', [AgentController::class, 'deactivate'])->name('deactivate');
    Route::get('/{agentCode}/analytics', [AgentController::class, 'analytics'])->name('analytics');
    Route::post('/interaction/{interactionId}/feedback', [AgentController::class, 'feedback'])->name('feedback');
    Route::get('/interaction/{interactionId}', [AgentController::class, 'interaction'])->name('interaction');
    Route::get('/{agentCode}/export', [AgentController::class, 'exportTrainingData'])->name('export');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('dashboard');
});
