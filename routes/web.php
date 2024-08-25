<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Route::view('/', 'welcome')->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('dashboard');
    });

    Route::view('profile', 'profile')
        ->name('profile');

    Volt::route('site-settings/logos', 'pages.site-settings.logos')
        ->middleware(['permission:show site settings|show logo settings'])
        ->name('site-settings.logos');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


require __DIR__.'/auth.php';

// Management
require __DIR__.'/management/settings.php';
require __DIR__.'/management/tools.php';
