<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Services\LocaleService;

class ApiLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to get locale from request input or header
        $locale = $request->input('locale') ?: $request->header('X-App-Locale');

        // Normalize and validate
        $normalized = LocaleService::normalize($locale);

        if ($normalized) {
            App::setLocale($normalized);
        }

        return $next($request);
    }
}
