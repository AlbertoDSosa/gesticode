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

    Volt::route('tools/invoces', 'pages.management.tools.invoices.index')
        ->name('management.tools.invoices');

    Volt::route('tools/invoces/create', 'pages.management.tools.invoices.create')
        ->name('management.tools.invoices.create');

    // Volt::route('tools/invoces/detail/{id}', 'pages.management.tools.invoices.detail')
    //     ->name('management.tools.invoices.detail');
});
