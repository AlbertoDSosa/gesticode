<?php

use App\Settings\GeneralSettings;
use App\Settings\LogoSettings;

if (! function_exists('getGeneralSettings')) {
    function getGeneralSettings($key) {
        return app(GeneralSettings::class)->$key ?? null;
    }
}

if (! function_exists('getLogoSettings')) {
    function getLogoSettings($key) {
        return app(LogoSettings::class)->$key ?? null;
    }
}



// function getSelected(): string {
//     if (request()->routeIs('users.*')) {
//         return 'tab_two';
//     } elseif (request()->routeIs('permissions.*')) {
//         return 'tab_three';
//     } elseif (request()->routeIs('roles.*')) {
//         return 'tab_three';
//     } elseif (request()->routeIs('database-backups.*')) {
//         return 'tab_four';
//     } elseif (request()->routeIs('general-settings.*')) {
//         return 'tab_five';
//     } elseif (request()->routeIs('dashboards.*')) {
//         return 'tab_one';
//     } else {
//         return 'tab_one';
//     }
// }
