<?php
namespace App\Http\Controllers\Admin;
use App\Helpers\SiteHelper;
use App\AdminModule\AdminListing;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Student\IndexStudent;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class RestoreController extends Controller {
    function Index(){
        //redirect to blocked students
        return redirect('/admin/restore/students');
    }

    //-------- CТУДЕНТЫ Которые заблокированые ----------------
    function StudentBlocked(IndexStudent $request) {
        $role=session('role');

        if (empty($role)) {
            return redirect('admin/');
        }
        if (!in_array($role, ['admin',  'franchisee'])){
            return redirect('admin/');

        }
        $request->franchisee_id=0;
        if ($role=='franchisee'){
            $request->franchisee_id=Auth::guard('franchisee')->user()->id;
        }
        if ($role=='teacher'){
            $request->teacher_id=Auth::guard('teacher')->user()->id;
        }
        // ЗАБЛОКИРОВАНЫЕ СПИСОК
        $data_block = AdminListing::create(Student::class)->processRequestAndGet($request,
            // set columns to query
            ['id', 'group_id',  'surname', 'lastname', 'patronymic','created_at as start_day',   'teacher_groups.name as groups','sum_aboniment', 'discount', 'balance','diams',  'blocking_date',  'blocking_reason','created_at as created_date'],

            // set columns to searchIn
            ['id', 'email', 'subcribe_email', 'surname', 'lastname', 'patronymic', 'dob', 'phone', 'language', 'blocking_reason' ,'blocking_date'],
            function ($query) use ($request) {
                $query->leftjoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id');
                $query ->where('student.blocked',1);
                $query ->where('student.deleted',0);
                if   ((!empty(  $request->franchisee_id) && (int)$request->franchisee_id>0)){
                    $query ->where('student.franchisee_id',(int)$request->franchisee_id);
                }
                if   ((!empty(  $request->teacher_id) && (int)$request->teacher_id>0)){
                    $query ->where('student.teacher_id',(int)$request->teacher_id);
                }
                if   ((!empty(  $request->group_id) && (int)$request->group_id>0)){
                    $query ->where('student.group_id',(int)$request->group_id);
                }
            }
        );
        if ($request->ajax()) {

            return ['data'=>$data_block];
        }

        return view('admin.restore.students.index', ['data_block'=>$data_block]);
    }
}
