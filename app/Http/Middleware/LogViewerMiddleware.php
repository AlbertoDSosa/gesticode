<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// use Illuminate\Support\Facades\App;
// use Illuminate\Support\Facades\Gate;
// use Opcodes\LogViewer\Facades\LogViewer;

class LogViewerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // if (
        //     config('log-viewer.require_auth_in_production', false)
        //     && App::isProduction()
        //     && ! Gate::has('viewLogViewer')
        //     && ! LogViewer::hasAuthCallback()
        // ) {
        //     return abort(403);
        // }

        if(!$request->user()) {
            return redirect('login');
        }

        if($request->user() && !$request->user()->hasAnyRole(['admin', 'super-admin'])) {
            return redirect('dashboard');
        }

        // LogViewer::auth();

        return $next($request);
    }
}
