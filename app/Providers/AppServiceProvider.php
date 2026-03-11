<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $local= env('APP_ENV', 'local');
        if ($local!='local'){
            error_reporting(E_ALL ^ E_NOTICE);
        }

        //
    }
}
