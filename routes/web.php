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
