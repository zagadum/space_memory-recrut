<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next)
    {

     //---- Для студентов


        if (!Auth::guard('recruting_student')->check()){

            return $next($request);
        }

        $userObj = Auth::guard('recruting_student')->user();

        $currentId = Session::getId();

        // Вариант A: если в users есть поле sess_id
        if (empty($userObj->blocked) && empty($userObj->deleted) && $userObj->id>0 && isset($userObj->sess_id) && $userObj->sess_id !== null) {
            if ($userObj->sess_id !== $currentId) {

                Auth::guard('recruting_student')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                  return redirect()->guest('/admin/login')->withErrors(['session' => __('auth.other_device')]);
            }
            return $next($request);
        }

        return $next($request);
    }
}
