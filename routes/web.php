<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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
   
    return view('welcome');
})->name('home');

// Market Research routes
use App\Http\Controllers\MarketResearchController;

Route::prefix('market-research')->name('market-research.')->group(function () {
    Route::get('/', [MarketResearchController::class, 'index'])->name('index');
    Route::post('/', [MarketResearchController::class, 'store'])->name('store');
    Route::get('/{id}', [MarketResearchController::class, 'show'])->name('show');
    Route::get('/{id}/status', [MarketResearchController::class, 'status'])->name('status');
    Route::post('/{id}/retry', [MarketResearchController::class, 'retry'])->name('retry');
    Route::get('/{id}/pdf', [MarketResearchController::class, 'downloadPdf'])->name('pdf');
    
    Route::middleware('auth')->group(function () {
        Route::get('/my-research/history', [MarketResearchController::class, 'history'])->name('history');
    });
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])->name('verify.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
