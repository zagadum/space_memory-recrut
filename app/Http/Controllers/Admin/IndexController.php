<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;



class IndexController extends Controller {
    function dashboard(){
        setcookie("per_page", 50, time() + 3600000000, "/"); //При входе добавить

        $role = session('role');

        if ($role=='admin'){
            return redirect('/admin/franchisees');
        }
        if ($role=='teacher'){
            return redirect('/admin/teacher-groups');
        }
        if ($role=='franchisee'){
            return redirect('/admin/teachers');
        }

        return view('admin.dashboard',[ ]);
    }
}
