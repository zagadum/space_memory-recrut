<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Closure;

/**
 * Check for login account is active
 *
 * Class CheckActive
 * @package App\Http\Middleware
 */
class CheckActive
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

        $id = !empty($request->user()->id) ? $request->user()->id : false;


        if(!empty($id)) {

            $user = $request->user();

            // if user is not active then logout and redirect to login page with
            // error message
            if(isset($user->status) && $user->status == 0) {
                Session::flash('error', 'Account is not active. Please contact the administrator.');
                Auth::guard()->logout();
                return redirect('/login');
            }
        }
        return $next($request);
    }
}
