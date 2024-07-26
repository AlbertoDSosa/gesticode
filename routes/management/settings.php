<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin'])->prefix('management')->group(function () {
    Volt::route('settings', 'pages.management.settings.index')
        ->name('management.settings');

    Volt::route('logos', 'pages.management.settings.logos')
        ->name('management.settings.logos');

    Volt::route('system', 'pages.management.settings.system')
        ->name('management.settings.system');


    Volt::route('roles', 'pages.management.settings.roles.index')
        ->name('management.settings.roles');

    Volt::route('permissions', 'pages.management.settings.permissions.index')
        ->name('management.settings.permissions');

});
