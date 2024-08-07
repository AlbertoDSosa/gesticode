<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('management')->group(function () {
    Volt::route('settings', 'pages.management.settings.index')
        ->name('management.settings');

    // General Settings
    Volt::route('settings/logos', 'pages.management.settings.logos')
        ->name('management.settings.logos');

    Volt::route('settings/system', 'pages.management.settings.system')
        ->name('management.settings.system');

    // Roles and Permissions
    Volt::route('settings/roles', 'pages.management.settings.roles.index')
        ->name('management.settings.roles');

    Volt::route('settings/roles/create', 'pages.management.settings.roles.create')
        ->name('management.settings.roles.create');

    Volt::route('settings/roles/edit/{role}', 'pages.management.settings.roles.edit')
        ->name('management.settings.roles.edit');

    Volt::route('settings/permissions', 'pages.management.settings.permissions.index')
        ->name('management.settings.permissions');

    Volt::route('settings/permissions/create', 'pages.management.settings.permissions.create')
        ->name('management.settings.permissions.create');

    Volt::route('settings/permissions/edit/{permission}', 'pages.management.settings.permissions.edit')
        ->name('management.settings.permissions.edit');

    // Users
    Volt::route('settings/users', 'pages.management.settings.users.index')
        ->name('management.settings.users');

    Volt::route('settings/users/create', 'pages.management.settings.users.create')
        ->name('management.settings.users.create');

    Volt::route('settings/users/edit/{user}', 'pages.management.settings.users.edit')
        ->name('management.settings.users.edit');
});
