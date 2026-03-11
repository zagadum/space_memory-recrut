<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Teacher\BulkDestroyTeacher;
use App\Http\Requests\Admin\Teacher\DestroyTeacher;
use App\Http\Requests\Admin\Teacher\IndexTeacher;
use App\Http\Requests\Admin\Teacher\StoreTeacher;
use App\Http\Requests\Admin\Teacher\UpdateTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Franchisee;
use App\Models\TeacherGroup;

use App\AdminModule\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $role = session('role', '');

            if (!in_array($role, ['admin', 'franchisee','manager'])) {
                abort(403, 'Доступ запрещен');
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @param IndexTeacher $request
     * @return array|Factory|View
     */
    public function index(IndexTeacher $request, $filter = '', $filter_id = 0)
    {

        if (!($request->ajax())) {
            session()->put('teacher_filter', $filter);
            session()->put('teacher_filter_id', (int)$filter_id);
            session()->save();
        }

        $role = session('role');
        if (empty($role))
        {
            die('Error role');
        }

        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }


        $request->franchisee_id = 0;
        $request->filter_id =(int) session('teacher_filter_id');
        $request->filter = session('teacher_filter');
        if ($role == 'franchisee') {
            $request->franchisee_id = Auth::guard('franchisee')->user()->id;
        }
        if ($role == 'franchisee') {
            $request->franchisee_id = Auth::guard('franchisee')->user()->id;
        }
        if ($role == 'teacher') {
            $request->teacher_id = Auth::guard('teacher')->user()->id;
        }
        $data = AdminListing::create(Teacher::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['id', 'surname', 'first_name', 'patronymic', 'enabled', 'teacher_group_count'],

            // set columns to searchIn
            ['id', 'surname', 'first_name', 'patronymic', 'phone', 'email', 'passport', 'iin', 'subscibe_email', 'language'],
            function ($query) use ($request) {

                $query->withCount(['Student'=>function ($query2) {$query2->where('student.deleted',  0)->where('student.blocked',  0); }]);

                $query->withCount(["TeacherGroup", 'TeacherGroup as teacher_group_count' => function ($query) {
                    $query->where('deleted', '=', 0);
                }])->get();
                $query->where('deleted', 0);
                if ((!empty($request->franchisee_id) && $request->franchisee_id > 0)) {
                    $query->where('franchisee_id', (int)$request->franchisee_id);
                }
                self::makeStudentQuery($query, $request);
            }

        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        $groupObj = [];

        if ($filter_id > 0 && $request->filter == 'franchisee') {
            $groupObj = Franchisee::where('id', (int)$filter_id)->first();
            $groupObj['name'] = $groupObj['surname'] . ' ' . $groupObj['first_name'];
            $groupObj['filterParam'] = $request->filter;
        }


        return view('admin.teacher.index', ['data' => $data, 'group_filter' => $groupObj]);
    }

    private function makeStudentQuery($query, $request)
    {

        if ((!empty($request->filter_id) && $request->filter_id > 0) && $request->filter == 'franchisee') {
            $query->where('teacher.franchisee_id', (int)$request->filter_id);
        }
        //--- Role Filter !!! important
        if ((!empty($request->franchisee_id) && $request->franchisee_id > 0)) {
            $query->where('franchisee_id', (int)$request->franchisee_id);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $franchisee_name = '';
        $teacher_def=[];

        $role = session('role');
        if (empty($role)) {
            die('Error role');
        }
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }

        if ($role == 'franchisee') {
            $franchisee_id = Auth::guard('franchisee')->user()->id;
            $FranchiseeList = Franchisee::select('id', 'first_name', 'surname', 'enabled')->where('deleted', 0)->where('enabled', 1)->where('id', (int)$franchisee_id)->first();
            $franchisee_name = $FranchiseeList['surname'] . ' ' . $FranchiseeList['first_name'];
        }
        if ($role == 'admin' or $role == 'manager') {
            $FranchiseeList = Franchisee::select('id', 'first_name', 'surname', 'enabled')->where('deleted', 0)->where('enabled', 1)->get();
        }

        // Get IP country code - handle with fallback
        try {
            $ipCountryCode = \App\Helpers\SiteHelper::GetIPInfo();
        } catch (\Throwable $e) {
            \Log::warning('Failed to get IP info: ' . $e->getMessage());
            $ipCountryCode = 'UA'; // Default fallback
        }
        $teacher_def['phone_country']=$ipCountryCode;

        return view('admin.teacher.create', ['Franchisee' => $FranchiseeList, 'franchisee_name' => $franchisee_name,'teacher_def'=>collect($teacher_def)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Teacher $teacher
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function edit($teacherId)
    {

        $role = session('role');
        if (empty($role)) {
            die('Error role');
        }
        $teacherId=(int)$teacherId;
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }

        $teacher = Teacher::find($teacherId);

        $teacher->load('Franchisee');
        $franchisee_name = '';

        if ($role == 'franchisee') {
            $franchisee_id =(int) Auth::guard('franchisee')->user()->id;
            $FranchiseeList = Franchisee::select('id', 'first_name', 'surname', 'enabled')->where('deleted', 0)->where('enabled', 1)->where('id', $franchisee_id)->first();
            $franchisee_name = $FranchiseeList['surname'] . ' ' . $FranchiseeList['first_name'];

        }
        if ($role == 'admin' || $role == 'manager') {
            $FranchiseeList = Franchisee::select('id', 'first_name', 'surname', 'enabled')->where('deleted', 0)->where('enabled', 1)->get();
        }
        $canBlock = self::enableBlock($teacherId);


        if (empty($teacher->phone_country)){
            $ipCountryCode=SiteHelper::GetIPInfo();
            if (empty($teacher->phone_country)){
                $teacher->phone_country=$ipCountryCode;
            }
        }

        return view('admin.teacher.edit', ['teacher' => $teacher, 'Franchisee' => $FranchiseeList, 'franchisee_name' => $franchisee_name, 'block' => $canBlock]);
    }

    private static function enableBlock($id){
        $id=(int)$id;
        $role = session('role');
        if (empty($role)) {
            die('Error role');
        }
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }

        $canBlock = true;
        $student = Student::where('teacher_id', $id)
            ->where('deleted', 0)
            ->first();
        $teacherGroup = TeacherGroup::where('teacher_id', $id)
            ->where('deleted', 0)
            ->first();
        if ($student || $teacherGroup) $canBlock = false;
        return $canBlock;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTeacher $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTeacher $request)
    {
        // Sanitize input
        $role = session('role');
        if (empty($role)) {
            die('Error role');
        }
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        $sanitized = $request->getSanitized();

        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');
        if ($role== 'franchisee') {
            $sanitized['franchisee_id'] =(int) Auth::guard('franchisee')->user()->id;
        } else {
            $sanitized['franchisee_id'] =(int) $request->getFranchiseeId();
        }

        $sanitized['password'] = Hash::make($sanitized['password']);
        $sanitized['enabled'] = 1;
        // Store the Teacher
        Teacher::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/teachers'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/teachers');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTeacher $request
     * @param Teacher $teacher
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTeacher $request,   $id)
    {
        // Sanitize input
        $id=(int)$id;
        $role = session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        $sanitized = $request->getSanitized();
        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');

        $sanitized['franchisee_id'] = $request->getFranchiseeId();
        if (empty($sanitized['franchisee_id'])) {
            unset($sanitized['franchisee_id']);
        }
        if (empty($sanitized['password'])) {
            unset($sanitized['password']);
        } else {
            $sanitized['password'] = Hash::make($sanitized['password']);
        }
        $teacher = Teacher::find($id);
        // Update changed values Teacher

        $teacher->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/teachers'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/teachers');
    }

    public function lock(Request $request, $id)
    {

        $id=(int)$id;
        $role = session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        $canBlock = self::enableBlock($id);
        if ($canBlock) {
            if ($id > 0) {
                $teacher = Teacher::find($id);
                if (isset($teacher)) {
                    if (empty($teacher->enabled)) {
                        $teacher->enabled = 1;
                    } else {
                        $teacher->enabled = 0;
                    }

                    $blocking_reason = $request->input('blocking_reason', '');
                    $teacher->blocking_date = date('Y-m-d');

                    if (!empty($blocking_reason)) {
                        $teacher->blocking_reason = $request->input('blocking_reason', '');
                    }

                    $teacher->save();
                }

                if ($request->ajax()) {
                    return response(['message' => trans('admin.operation.succeeded')]);
                }

            } else {
                return response(['message' => 'error']);
            }
            return redirect()->back();
        } else return redirect()->back();
    }

    public function unlock(Request $request, $id)
    {
        $id=(int)$id;
        $role = session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        $canBlock = self::enableBlock($id);
        if ($canBlock) {
            if ($id > 0) {
                $teacher = Teacher::find($id);
                if (isset($teacher)) {
                    $teacher->enabled = 1;
                    $teacher->save();
                }

                if ($request->ajax()) {
                    return response(['message' => trans('admin.operation.succeeded')]);
                }

            } else {
                return response(['message' => 'error']);
            }
            return redirect()->back();
        } else return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTeacher $request
     * @param Teacher $teacher
     * @return ResponseFactory|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyTeacher $request, Teacher $teacher)
    {
        // ...existing code...
        $role = session('role');
        if (!in_array($role,['admin' ,'franchisee','manager'])){
            return redirect('/logout');
        }
        // ...existing code...
        if (isset($teacher) && !empty($teacher->id)) {
            $teacher->deleted = 1;
            $teacher->save();
        }
        //$teacher->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }


}
