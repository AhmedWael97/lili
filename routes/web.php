<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FacebookOAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Marketing\MarketingOSController;
use App\Http\Controllers\MarketResearchWebController;
use App\Models\Package;
use Illuminate\Support\Facades\Route;




Route::get('/new-migrate',function() {
    \Artisan::call("migrate");
    return "migrated";
});

Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('view:clear');
    return "Cache Cleared!";
});


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
    Route::get('/agents', [DashboardController::class, 'agents'])->name('agents');
    Route::get('/platforms', [DashboardController::class, 'platforms'])->name('platforms');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
});

// Marketing OS routes (Phase 1 - Strategy-Only)
Route::middleware(['auth', 'subscription.active'])->prefix('marketing/os')->name('marketing.os.')->group(function () {
    Route::get('/', [MarketingOSController::class, 'index'])->name('index');
    Route::get('/setup-brand', [MarketingOSController::class, 'setupBrand'])->name('setup-brand');
    Route::post('/store-brand', [MarketingOSController::class, 'storeBrand'])->name('store-brand');
    Route::post('/generate-strategy', [MarketingOSController::class, 'generateStrategy'])->name('generate-strategy');
    Route::get('/strategy/{id}', [MarketingOSController::class, 'viewStrategy'])->name('view-strategy');
    Route::post('/add-competitor', [MarketingOSController::class, 'addCompetitor'])->name('add-competitor');
    Route::delete('/competitor/{id}', [MarketingOSController::class, 'deleteCompetitor'])->name('delete-competitor');
    Route::get('/competitor/{id}/keywords', [MarketingOSController::class, 'getCompetitorKeywords'])->name('competitor-keywords');
    Route::get('/competitor/{id}/backlinks', [MarketingOSController::class, 'getCompetitorBacklinks'])->name('competitor-backlinks');
    Route::get('/competitor/{id}/social', [MarketingOSController::class, 'getCompetitorSocial'])->name('competitor-social');
    Route::get('/export-strategy/{id}', [MarketingOSController::class, 'exportStrategy'])->name('export-strategy');
});

// Market Research routes
Route::middleware(['auth', 'subscription.active'])->prefix('market-research')->name('market-research.')->group(function () {
    Route::get('/', [MarketResearchWebController::class, 'index'])->name('index');
    Route::get('/requests', [MarketResearchWebController::class, 'requests'])->name('requests');
    Route::get('/report/{id}', [MarketResearchWebController::class, 'show'])->name('report');
});

// Agent Management routes
Route::middleware(['auth', 'subscription.active'])->prefix('agents')->name('agents.')->group(function () {
    Route::get('/', [AgentController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])->name('dashboard');
    Route::post('/{agentCode}/activate', [AgentController::class, 'activate'])->name('activate');
    Route::get('/{agentCode}/use', [AgentController::class, 'use'])->name('use');
    Route::delete('/{agentCode}/deactivate', [AgentController::class, 'deactivate'])->name('deactivate');
    
    // Agent Onboarding routes
    Route::get('/{agentCode}/onboarding', [\App\Http\Controllers\AgentOnboardingController::class, 'show'])->name('onboarding');
    Route::post('/{agentCode}/onboarding', [\App\Http\Controllers\AgentOnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/{agentCode}/onboarding/edit', [\App\Http\Controllers\AgentOnboardingController::class, 'edit'])->name('onboarding.edit');
    Route::put('/{agentCode}/onboarding', [\App\Http\Controllers\AgentOnboardingController::class, 'update'])->name('onboarding.update');
    
    Route::get('/{agentCode}/analytics', [AgentController::class, 'analytics'])->name('analytics');
    Route::post('/interaction/{interactionId}/feedback', [AgentController::class, 'feedback'])->name('feedback');
    Route::get('/interaction/{interactionId}', [AgentController::class, 'interaction'])->name('interaction');
    Route::get('/{agentCode}/export', [AgentController::class, 'exportTrainingData'])->name('export');
});

// QA Agent routes
Route::middleware(['auth', 'subscription.active'])->prefix('qa-agent')->name('qa-agent.')->group(function () {
    Route::get('/', [\App\Http\Controllers\QAAgentController::class, 'index'])->name('index');
    Route::post('/test-plan', [\App\Http\Controllers\QAAgentController::class, 'generateTestPlan'])->name('generate-test-plan');
    Route::post('/analyze-bugs', [\App\Http\Controllers\QAAgentController::class, 'analyzeBugs'])->name('analyze-bugs');
    Route::post('/generate-tests', [\App\Http\Controllers\QAAgentController::class, 'generateAutomatedTests'])->name('generate-tests');
    Route::post('/test-cases', [\App\Http\Controllers\QAAgentController::class, 'generateTestCases'])->name('test-cases');
    Route::post('/security-analysis', [\App\Http\Controllers\QAAgentController::class, 'analyzeSecurityVulnerabilities'])->name('security-analysis');
    Route::post('/bug-report', [\App\Http\Controllers\QAAgentController::class, 'generateBugReport'])->name('bug-report');
    Route::post('/review-pr', [\App\Http\Controllers\QAAgentController::class, 'reviewPullRequest'])->name('review-pr');
    Route::post('/execute-live-test', [\App\Http\Controllers\QAAgentController::class, 'executeLiveTest'])->name('execute-live-test');
    Route::post('/web-test-plan', [\App\Http\Controllers\QAAgentController::class, 'generateWebTestPlan'])->name('web-test-plan');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('dashboard');
});
