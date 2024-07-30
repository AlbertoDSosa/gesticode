<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('dashboard');
    });

    Route::view('profile', 'profile')
        ->name('profile');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


require __DIR__.'/auth.php';

// Management
require __DIR__.'/management/settings.php';
require __DIR__.'/management/tools.php';
