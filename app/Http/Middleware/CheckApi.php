<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckApi
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
            if ($userObj->id && empty($userObj->blocked) && $userObj->deleted==0) {
                return $next($request);
            }
        }


         return   redirect('/api/not-auth');

    }
}
