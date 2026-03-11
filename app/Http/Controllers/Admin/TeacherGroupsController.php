<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Student\IndexStudent;
use App\Http\Requests\Admin\TeacherGroup\BulkDestroyTeacherGroup;
use App\Http\Requests\Admin\TeacherGroup\DestroyTeacherGroup;
use App\Http\Requests\Admin\TeacherGroup\IndexTeacherGroup;
use App\Http\Requests\Admin\TeacherGroup\StoreTeacherGroup;
use App\Http\Requests\Admin\TeacherGroup\UpdateTeacherGroup;
use App\Models\Ads;
use App\Models\City;
use App\Models\Country;
use App\Models\Franchisee;
use App\Models\Region;
use App\Models\Student;
use App\Models\StudentGroupTask;
use App\Models\StudentTrainingTask;
use App\Models\Teacher;
use App\Models\TeacherGroup;
use App\Models\Age;

use App\AdminModule\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class TeacherGroupsController extends Controller
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
     * Display a listing of the resource.
     *
     * @param IndexTeacherGroup $request
     * @return array|Factory|View
     */
    public function index(IndexTeacherGroup $request,  $group_filter ='',  $group_filter_id=0)
    {
        $role=session('role');
        $group_filter_id=(int)$group_filter_id;
        if (empty($role))
        {
            die('Error role');
        }
        $role = session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){

         return redirect('/logout');
        }

        if (!($request->ajax())) {
            session()->put('group_filter',$group_filter);
            session()->put('group_filter_id',  $group_filter_id);
            session()->save();
        }
        $request->franchisee_id=0;
        $request->group_filter_id= session('group_filter_id');
        $request->group_filter= session('group_filter');


        if ($role=='teacher'){
            $request->teacher_id=Auth::guard('teacher')->user()->id;
        }
        if ($role=='franchisee'){
            $request->franchisee_id=Auth::guard('franchisee')->user()->id;
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TeacherGroup::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['id',   'name', 'age.name as age_name','age_id', 'address', 'locations', 'start_day',  'enabled'  ],

            // set columns to searchIn
            ['id', 'name', 'address', 'locations', 'start_day','enabled'],
            function ($query) use ($request) {

                $query->withCount(['Student'=>function ($query2) {$query2->where('student.deleted',  0)->where('student.blocked',  0); }]);//->where('student.blocked',  0);
                $query->with("Age");
                $query->leftjoin('age', 'age.id', '=', 'teacher_groups.age_id');
                $query->where('teacher_groups.deleted',0);
                self::makeGroupQuery($query,$request);
            }
        );

        if (!empty($data)){
            foreach ($data as &$items){
                if (isset( $items->Age->name)){
                    $items->age_name= $items->Age->name;
                }else{
                    $items->age_name='';
                }

            }
        }
        if ($request->ajax()) {

            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        $groupObj=[];
        if ($group_filter_id>0 && $request->group_filter =='teacher'){
            $groupObj=Teacher::where('id',(int)$group_filter_id)->first();
            $groupObj['name']= $groupObj['surname'].' '.$groupObj['first_name'];
            $groupObj['filterParam'] =$request->group_filter;
        }
        if ($group_filter_id>0 && $request->group_filter =='franchisee'){
            $groupObj=Franchisee::where('id',(int)$group_filter_id)->first();
            $groupObj['name']= $groupObj['surname'].' '.$groupObj['first_name'];
            $groupObj['filterParam'] =$request->group_filter;
        }


        return view('admin.teacher-group.index', ['data' => $data, 'group_filter'=>$groupObj]);
    }

    private function makeGroupQuery($query, $request) {

        if   ((!empty(  $request->group_filter_id) && (int)$request->group_filter_id>0) && $request->group_filter =='teacher'){
            $query ->where('teacher_groups.teacher_id',(int)$request->group_filter_id);
        }
        if   ((!empty(  $request->group_filter_id) && (int)$request->group_filter_id>0) && $request->group_filter =='franchisee'){
            $query ->where('teacher_groups.franchisee_id',(int)$request->group_filter_id);
        }
        //--- Role Filter !!! important
        if   ((!empty(  $request->teacher_id) && (int)$request->teacher_id>0)){
            $query ->where('teacher_groups.teacher_id',(int)$request->teacher_id);
        }
        if   ((!empty(  $request->franchisee_id) && $request->franchisee_id>0)){
            $query ->where('teacher_groups.franchisee_id',(int)$request->franchisee_id);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create(){
        $teacherGroupDef=['franchisee'=>[],'teacher'=>[],'age'=>[],'region'=>[],'city'=>[]];
        $role=session('role');

        if (empty($role)) {
            die('Error role');
        }

        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }


        if ($role=='admin') {
            $FranchiseeList = Franchisee::select('id', 'first_name', 'surname')->get();
        }
        //------------- Role franchisee /begin
         if ($role=='franchisee'){
             $franchisee_id =Auth::guard('franchisee')->user()->id;
             $franchisee_id=(int)$franchisee_id;
             $teacherGroupDef['franchisee_id'] =$franchisee_id;
             $teacherGroupDef['franchisee']['id'] =$franchisee_id;
             $FranchiseeList=Franchisee::select('id','first_name','surname')->where('id',$franchisee_id)->get();
          }
          //------------- Role franchisee \end

        //------------- Role franchisee /begin
        if ($role=='teacher'){

            $teacher_id =Auth::guard('teacher')->user()->id;
            $franchisee_id =Auth::guard('teacher')->user()->franchisee_id;
            $teacherGroupDef['franchisee_id'] =$franchisee_id;
            $teacherGroupDef['teacher_id'] = (int)$teacher_id;
            $teacherGroupDef['franchisee']['id'] =(int)$franchisee_id;
            $FranchiseeList=Franchisee::select('id','first_name','surname')->where('id',$franchisee_id)->get();
            $teacherGroupDef['teacher']['id'] = (int)$teacher_id;
            $teacherGroupDef['teacher']['surname'] = Auth::guard('teacher')->user()->surname;
            $teacherGroupDef['teacher']['first_name'] = Auth::guard('teacher')->user()->first_name;

        }
        //------------- Role franchisee \end
        return view('admin.teacher-group.create' ,['Age'=>Age::all(),  'Franchisee' => $FranchiseeList,'teacherGroup'=>collect($teacherGroupDef)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTeacherGroup $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTeacherGroup $request)
    {
        $role=session('role');
        if (empty($role)) {
            die('Error role');
        }
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){

            return redirect('/logout');
        }
        // Sanitize input
        $sanitized = $request->getSanitized();



        $sanitized['franchisee_id'] =  $request->getFranchiseeId();
        if ($role=='franchisee'){
            $sanitized['franchisee_id'] =Auth::guard('franchisee')->user()->id;
        }
        $sanitized['franchisee_id'] =(int)  $sanitized['franchisee_id'] ;
        $sanitized['teacher_id'] =(int) $request->getTeacherId();
        $sanitized['region_id'] =(int) $request->getRegionId();
        $sanitized['city_id'] =(int) $request->getCityId();
        $sanitized['age_id'] =(int) $request->getAgeId();
        $sanitized['start_day'] = date('Y-m-d');
        // Store the TeacherGroup
        if (isset($sanitized['franchisee_id']) && (int)$sanitized['franchisee_id']>0){
            $sanitized['country_id'] =Franchisee::where('id',$sanitized['franchisee_id'])->first()['country_id'];
        }
         TeacherGroup::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/teacher-groups'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/teacher-groups');
    }


    public function upload(TeacherGroup $teacherGroup,$groupId,Request $request)
    {
        $success=false;
        $role=session('role');
        if (empty($role)) {
            die('Error role');
        }
        if (in_array($role,['franchisee','admin','manager'])){

            $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif']);
            $fileBaner = $request->file('photo');
            $url='';
            if ($fileBaner) {
                $TeacherGroup = TeacherGroup::find($groupId);
                $filename =  $TeacherGroup->id. '_zim.png';
                $path = public_path('useruploads/news/' . $filename);
                if (file_exists($path . $filename)) {
                    @unlink($path . $filename);
                }
                Image::make($fileBaner)->orientate()->fit(140, 140)->save($path);

                $url =  '/useruploads/news/' . $filename;
                $TeacherGroup->update(['zoom_img' =>$url]);
                $success = true;
            }

        }

        return response()->json(['photoUrl'=>$url,'success'=>$success]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TeacherGroup $teacherGroup
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Request $request, $groupId)
    {
        $groupId = (int) $groupId;
        $teacherGroup = TeacherGroup::findOrFail($groupId);

        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }

       // $this->authorize('admin.teacher-group.edit', $teacherGroup);
        $teacherGroup->load('Franchisee');
        $teacherGroup->load('Teacher');
        $teacherGroup->load('Country');
        $teacherGroup->load('Region');
        $teacherGroup->load('City');
        $teacherGroup->load('Age');




       $CityObj =collect([]);
        if (isset($teacherGroup->region_id) && $teacherGroup->region_id>0){
            $CityObj=City::where('region_id',(int)$teacherGroup->region_id)->get();
            if (empty($CityObj)){
                $CityObj =collect([]);
            }
        }
       if ($teacherGroup->franchisee_id>0){
           $FranciseObj=Franchisee::where('id',(int)$teacherGroup->franchisee_id)->first();

       }

        $FranchiseeList=Franchisee::select('id','first_name','surname')->where('deleted',0)->get();

       //-------- Add List Groum

        $data = $this->tableStudent($request, $groupId);

        return view('admin.teacher-group.edit', ['teacherGroup' => $teacherGroup,'Age'=>Age::all(),'Franchisee' =>$FranchiseeList,'data'=>$data]);
    }
    private function tableStudent(Request $request,$groupId)
    {
        $groupId=(int)$groupId;
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }

        if ($role=='teacher'){
            $request->teacher_id=Auth::guard('teacher')->user()->id;
        }
        if ($role=='franchisee'){
            $request->franchisee_id=Auth::guard('franchisee')->user()->id;
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Student::class)->processRequestAndGet($request,
            // set columns to query
            ['id', 'surname', 'lastname', 'patronymic', 'group_id', 'teacher_id', 'teacher_groups.start_day', 'teacher_groups.name as groups', 'teacher.surname as teacher_surname', 'teacher.first_name as teacher_first_name', 'sum_aboniment', 'discount', 'balance','blocked'],
            // set columns to searchIn
            [],

            function ($query) use ($request,$groupId) {

                $query->leftjoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id');
                $query->leftjoin('teacher', 'teacher.id', '=', 'student.teacher_id');
                $query ->where('student.deleted',0);
             //   $query ->where('student.blocked',0);
                $query ->where('student.group_id',$groupId);
                if   ((!empty(  $request->teacher_id) && (int)$request->teacher_id>0)){
                    $query ->where('teacher_groups.teacher_id',(int)$request->teacher_id);
                }
                if   ((!empty(  $request->franchisee_id) && $request->franchisee_id>0)){
                    $query ->where('teacher_groups.franchisee_id',(int)$request->franchisee_id);
                }
            }
        );

        return $data;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTeacherGroup $request
     * @param TeacherGroup $teacherGroup
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTeacherGroup $request, $groupId)
    {



        $groupId=(int)$groupId;
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        if (empty($groupId) || $groupId<=0){
        die('Error Group ID');

        }


        // Sanitize input
        $sanitized = $request->getSanitized();

        $sanitized['franchisee_id'] = $request->getFranchiseeId();

        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =(int)$franchisee_id;
        }
        //------------- Role franchisee \end

        $sanitized['teacher_id'] = (int)$request->getTeacherId();
        //     $sanitized['country_id'] = $request->getCountryId();
        $sanitized['region_id'] = (int)$request->getRegionId();
        $sanitized['city_id'] =(int) $request->getCityId();
        $sanitized['age_id'] = (int)$request->getAgeId();
        $sanitized['enabled'] = 1;

        // Store the TeacherGroup
        if (isset($sanitized['franchisee_id']) && (int)$sanitized['franchisee_id']>0){
            $sanitized['country_id'] =Franchisee::where('id',$sanitized['franchisee_id'])->first()['country_id'];
        }

        if (empty( $sanitized['franchisee_id'])){
            unset( $sanitized['franchisee_id']);
        }
        if (empty( $sanitized['teacher_id'])){
            unset( $sanitized['teacher_id']);
        }

        if (empty( $sanitized['country_id'])){
            unset( $sanitized['country_id']);
        }
        if (empty( $sanitized['region_id'])){
            unset( $sanitized['region_id']);
        }
        if (empty( $sanitized['city_id'])){
            unset( $sanitized['city_id']);
        }
        if (empty( $sanitized['age_id'])){
            unset( $sanitized['age_id']);
        }

        $teacherGroup=TeacherGroup::find($groupId);
        if (empty( $teacherGroup->start_day)) {
            $sanitized['start_day'] = date('Y-m-d');
        }
        // Update changed values TeacherGroup

        //----- при смене группы меняем учителя у студентов и заданий
        if ($teacherGroup->teacher_id != $sanitized['teacher_id'] && isset( $sanitized['teacher_id']) && $sanitized['teacher_id']>0) {

            Student::where('group_id', $teacherGroup->id)->update(['teacher_id' => (int)$sanitized['teacher_id']]);
            StudentGroupTask::where('group_id', $teacherGroup->id)->update(['teacher_id' => (int)$sanitized['teacher_id']]);
            //StudentTrainingTask::where('group_id', $teacherGroup->id)->where('teacher_id',$teacherGroup->teacher_id )->update(['teacher_id' => (int)$sanitized['teacher_id']]);
           // StudentTrainingTask::where('group_id', $teacherGroup->id)->update(['teacher_id' => (int)$sanitized['teacher_id']]);
        }

        $teacherGroup->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/teacher-groups'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/teacher-groups');
    }
    public function lock(DestroyTeacherGroup  $request,$id)
    {
        $id=(int)$id;
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])){
            return redirect('/logout');
        }
        if ($id>0){
            $teacherGroup=TeacherGroup::find($id);

            //------------- Role franchisee /begin
            if (session('role')=='franchisee'){
                $franchisee_id =Auth::guard('franchisee')->user()->id;
                //------ Запрет на чужую фанзизу
                if ($teacherGroup->franchisee_id!=$franchisee_id){
                    if ($request->ajax()) {
                        return response(['message' => trans('admin.operation.error')]);
                    }
                    return redirect()->back();
                }
            }
            //------------- Role franchisee \end

            if (isset($teacherGroup)){
                if (empty($teacherGroup->enabled)){
                    $teacherGroup->enabled=1;
                }else{
                    $teacherGroup->enabled=0;
                }
                $teacherGroup->save();
            }

            if ($request->ajax()) {
                return response(['message' =>trans('admin.operation.succeeded')]);
            }

        }else{
            return response(['message' =>'error']);
        }
        return redirect()->back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTeacherGroup $request
     * @param TeacherGroup $teacherGroup
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTeacherGroup $request, TeacherGroup $teacherGroup)
    {
        // ...existing code...
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            //------ Запрет на чужую фанзизу
            if ($teacherGroup->franchisee_id!=$franchisee_id){
                if ($request->ajax()) {
                    return response(['message' => trans('admin.operation.error')]);
                }
                return redirect()->back();
            }
        }
        //------------- Role franchisee \end
        if (isset($teacherGroup)  && !empty($teacherGroup->id)){
            $teacherGroup->deleted=1;
            $teacherGroup->save();
        }
      //  $teacherGroup->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }



    public function GetTeacherByFrancisee($franchisee_id){
        $franchisee_id=(int)$franchisee_id;
        $jsonRet=['teacher'=>[],'region'=>[],'city'=>[]];
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
        }

        if ($franchisee_id>0){
            //->where('enabled',1)
            $jsonRet['teacher']=Teacher::where('franchisee_id',$franchisee_id)->select('id','surname','first_name')->where('deleted',0)->get()->toArray();//DB::raw("CONCAT(surname,' ',first_name) AS surname"),
               $FranchiseeRow= Franchisee::where('id',$franchisee_id)->first();
               if ($FranchiseeRow['country_id']>0){
                   $jsonRet['region']= Region::where('country_id',$FranchiseeRow['country_id'])->select('id','name')->get()->toArray();
               }
            if ($FranchiseeRow['region_id']>0){
                $jsonRet['city']= City::where('region_id',$FranchiseeRow['region_id'])->select('id','name')->get()->toArray();
            }
        }
        if (empty($jsonRet)){
            $jsonRet=['teacher'=>[],'region'=>[],'city'=>[]];
        }
        return response()->json($jsonRet);
    }
}
