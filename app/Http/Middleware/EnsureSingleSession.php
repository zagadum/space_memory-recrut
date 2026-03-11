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


        if (!Auth::guard('student')->check()){

            return $next($request);
        }

//        dd( [
//            'student_check' => Auth::guard('student')->check(),
//            'admin_check' => Auth::guard('admin')->check(),
//            'teacher_check' => Auth::guard('teacher')->check(),
//            'student_user'  => Auth::guard('student')->user(),
//            'default_check' => Auth::check(),
//            'admin_id'      => Auth::guard('admin')->id(),
//            'teacher_id'    => Auth::guard('teacher')->id(),
//            'franchisee_id' => Auth::guard('franchisee')->id(),
//            'auth_header'   => $request->header('Authorization'),
//            'cookie'        => $request->cookie(config('session.cookie')),
//        ]);

        $userObj = Auth::guard('student')->user();

        $currentId = Session::getId();

      //  dd(Auth::guard('student'));
        // Вариант A: если в users есть поле sess_id
        if (empty($userObj->blocked) && empty($userObj->deleted) && $userObj->id>0 && isset($userObj->sess_id) && $userObj->sess_id !== null) {
            if ($userObj->sess_id !== $currentId) {

                Auth::guard('student')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                  return redirect()->guest('/admin/login')->withErrors(['session' => __('auth.other_device')]);
            }
            return $next($request);
        }

        return $next($request);
    }
}
