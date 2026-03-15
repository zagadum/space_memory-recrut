<?php

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetStudentLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if (Auth::guard('recruting_student')->check()) {
            $student = Auth::guard('recruting_student')->user();
            $locale = LocaleService::normalize($student->language ?? null);
        }

        App::setLocale($locale ?? config('app.locale', LocaleService::getDefault()));

        return $next($request);
    }
}

