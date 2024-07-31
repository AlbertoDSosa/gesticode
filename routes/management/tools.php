<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('management')->group(function () {
    Volt::route('tools', 'pages.management.tools.index')
        ->name('management.tools');

    Volt::route('tools/backups', 'pages.management.tools.backups.index')
        ->name('management.tools.backups');

    Volt::route('tools/logs/user-activity', 'pages.management.tools.logs.user-activity')
        ->name('management.tools.logs.user-activity');
});
