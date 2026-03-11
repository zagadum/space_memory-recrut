<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAuth
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
        $practicant_id = session('external.practicant_id', 0);
        if (!empty($practicant_id)){
            return   redirect('/olympiad/the-finish');
        }
        if (Auth::guard('student')->check()){

            $userObj=Auth::guard('student')->user();
            if ($userObj->id && empty($userObj->blocked)) {
                return $next($request);
            }
        }
        if (Auth::guard('admin')->check() ){
            $userObj=Auth::guard('admin')->user();
            if (isset($userObj) && $userObj->id > 0 &&  $userObj->deleted==0) {
                return $next($request);
            }
        }elseif  (Auth::guard('teacher')->check()) {

            $userObj=Auth::guard('teacher')->user();

            if (isset($userObj) && $userObj->id > 0 && $userObj->enabled==1 && $userObj->deleted==0) {

                return $next($request);
            }
        }elseif  (Auth::guard('franchisee')->check()) {
            $userObj = Auth::guard('franchisee')->user();
            if (isset($userObj) && $userObj->id > 0 && $userObj->enabled==1 && $userObj->deleted==0) {
                return $next($request);
            }
        }
            return   redirect('/');

    }
}
