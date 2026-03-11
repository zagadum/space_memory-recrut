<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Services\LocaleService;

/**
 * SetStudentLocale Middleware
 *
 * Sets the application locale based on the authenticated student's language preference
 */
class SetStudentLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();

            // Validate and set locale only if valid
            if ($student->language && LocaleService::isValid($student->language)) {
                App::setLocale($student->language);
            }
        }

        return $next($request);
    }
}
