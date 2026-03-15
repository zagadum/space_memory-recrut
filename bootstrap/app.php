<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest.admin' => \App\AdminModule\AdminAuth\Http\Middleware\RedirectIfAuthenticated::class,
            'verify.jwt'  => \App\Http\Middleware\VerifyJwtToken::class,
            'api.locale'  => \App\Http\Middleware\ApiLocaleMiddleware::class,
            'register.form' => \App\Http\Middleware\EnsureRegisterFormRequest::class,
            'verify.form'   => \App\Http\Middleware\EnsureRegisterFormRequest::class,
        ]);

        $middleware->group('is_student', [
            \App\Http\Middleware\CheckStudent::class,
            \App\Http\Middleware\SetStudentLocale::class,
        ]);

        $middleware->group('is_auth', [
            \App\Http\Middleware\CheckAuth::class,
            \App\Http\Middleware\SetStudentLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
