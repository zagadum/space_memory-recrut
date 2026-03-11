<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
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

        if (Auth::guard('admin')->check()){
            $userObj=Auth::guard('admin')->user();
            if (isset($userObj) && $userObj->id > 0 &&   $userObj->deleted==0) {
                return $next($request);
            }
        }
        return   redirect('/');
    }
}
