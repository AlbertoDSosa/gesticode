<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
// use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // LogViewer::auth(function ($request) {
        //     return $request->user() && $request->user()->hasAnyRole(['admin', 'super-admin']);
        // });
    }
}
