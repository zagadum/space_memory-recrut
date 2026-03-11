<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiteClosed
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

        if (in_array($_SERVER['REMOTE_ADDR'],['91.214.85.205','188.163.73.239','217.120.165.109','2001:1c05:2703:bc00:dc3e:8bf2:9c9e:fae3'])){
            return $next($request);
        }


        if (config('app.site_closed')) {
             return response()->view('close_site.index');
        }

        return $next($request);
    }
}
