<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckStudent
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


        if (Auth::guard('student')->check()){

            $userObj=Auth::guard('student')->user();

            if (isset($userObj) && $userObj->id>0 && $userObj->deleted==0 && empty($userObj->blocked) ) {
                return $next($request);
            }
        }
        return   redirect('/');
    }
}
