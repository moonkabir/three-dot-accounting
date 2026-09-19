<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings/company',   [\App\Http\Controllers\Settings\CompanyController::class, 'edit'])->name('settings.company');
    Route::post('/settings/company',  [\App\Http\Controllers\Settings\CompanyController::class, 'update'])->name('settings.company.update');
});

require __DIR__.'/auth.php';

