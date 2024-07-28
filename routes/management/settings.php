<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('management')->group(function () {
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

    Volt::route('roles/create', 'pages.management.settings.roles.create')
        ->name('management.settings.roles.create');

    Volt::route('roles/edit/{role}', 'pages.management.settings.roles.edit')
        ->name('management.settings.roles.edit');

    Volt::route('permissions/create', 'pages.management.settings.permissions.create')
        ->name('management.settings.permissions.create');

    Volt::route('permissions/edit/{permission}', 'pages.management.settings.permissions.edit')
        ->name('management.settings.permissions.edit');
});
