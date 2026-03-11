<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

class IndexController extends Controller {
    function dashboard(){
        $role = session('role');

 

        $retTemplate['is_dashboard']=1;

        return view('student.home.dashboard',$retTemplate);
    }
}
