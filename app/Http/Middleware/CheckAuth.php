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

        if (Auth::guard('recruting_student')->check()){

            $userObj=Auth::guard('recruting_student')->user();
            if ($userObj->id && empty($userObj->blocked)) {
                return $next($request);
            }
        }

        if ($request->is('father/*')) {
            return redirect()->route('father.login');
        }
        return redirect('/');

    }
}
