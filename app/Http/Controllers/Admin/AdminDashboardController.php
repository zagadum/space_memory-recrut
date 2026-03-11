<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Inspiring;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $role = session('role', '');

            if (!in_array($role, ['admin', 'franchisee','teacher','manager'])) {
                abort(403, 'Доступ запрещен');
            }
            return $next($request);
        });

    }
    /**
     * Display default admin home page
     *
     * @return Factory|View
     */
    public function index()
    {
        setcookie("per_page", 50, time()+3600000000, "/"); //При входе добавить
        if ( (session('role')=='admin')  ){
            return redirect('/admin/franchisees');
        }
        if ( (session('role')=='franchisee')  ){
            return redirect('/admin/teachers');
        }

        if (session('role')=='teacher' || session('role')=='manager' ){
            return redirect('/admin/teacher-groups');
        }

        return view('admin.dashboard.index', [
            'inspiration' => Inspiring::quote()
        ]);
    }

    public function term_use(){
        return view('admin.page-info.term_use');
    }
}
