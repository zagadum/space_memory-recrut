<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Requests\Admin\Student\IndexMoneyLog;
use App\Models\GameBalanceLog;
use App\Services\Game\CoinService;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Password_Email\UpdateEmail;
use App\Http\Requests\Admin\Password_Email\UpdatePassword;
use App\Http\Requests\Admin\Student\BulkDestroyStudent;
use App\Http\Requests\Admin\Student\DestroyStudent;
use App\Http\Requests\Admin\Student\IndexStudent;
use App\Http\Requests\Admin\Student\StoreStudent;
use App\Http\Requests\Admin\Student\UpdateStudent;



use App\Mail\ChangeEmail;
use App\Mail\RestoreMail;
use App\Models\Franchisee;
use App\Models\PaymentPeriod;

use App\Models\Student;

use App\Models\StudentGroupTask;
use App\Models\StudentTrainingTask;
use App\Models\Teacher;
use App\Models\TeacherGroup;
use App\Models\StudentPayment;
use App\Services\Game\PlayerService;
use App\Services\LocaleService;

use App\AdminModule\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class StudentController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param IndexStudent $request
     * @return array|Factory|View
     */

    public function index(IndexStudent $request, $filter ='',  $filter_id=0) {

        $role=session('role');

        if (empty($role)) {
            return redirect('admin/logout');
            die('Error role. ');
        }
        $filter_id=(int)$filter_id;
        if (!($request->ajax())) {
            session()->put('filter', $filter);
            session()->put('filter_id', $filter_id);
            session()->save();
        }
        $request->franchisee_id=0;
        $request->filter_id=(int) session('filter_id');
        $request->filter= session('filter');




        if ($role=='franchisee'){
            $request->franchisee_id=Auth::guard('franchisee')->user()->id;
        }
        if ($role=='teacher'){
            $request->teacher_id=Auth::guard('teacher')->user()->id;
        }

        $data = AdminListing::create(Student::class)->processRequestAndGet($request,
            // set columns to query
            ['id', 'surname', 'lastname', 'patronymic', 'group_id', 'teacher_id', 'created_at', 'teacher_groups.start_day', 'teacher_groups.name as groups', 'teacher.surname as teacher_surname',
                'teacher.first_name as teacher_first_name', 'sum_aboniment', 'discount', 'balance','diams','rang_level','rang as rang_max','blocked', 'blocking_date'],
            // set columns to searchIn
            ['surname', 'lastname', 'patronymic','teacher_groups.name','teacher.surname', 'teacher.first_name','email', 'subcribe_email', 'dob','phone' ],
            function ($query) use ($request) {
                $query->leftjoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id');
                $query->leftjoin('teacher', 'teacher.id', '=', 'student.teacher_id');
                $query ->where('student.deleted',0);
                $query ->where('student.blocked',0);
                self::makeStudentQuery($query,$request);
            }
        );
        // ЗАБЛОКИРОВАНЫЕ СПИСОК
//        $data_block = AdminListing::create(Student::class)->processRequestAndGet($request,
//            // set columns to query
//            ['id', 'group_id',  'surname', 'lastname', 'patronymic','created_at','created_at as start_day',    'teacher_groups.name as groups','sum_aboniment', 'discount', 'balance','diams', 'blocking_date', 'blocking_reason','blocked'],
//
//            // set columns to searchIn
//            ['id', 'email', 'subcribe_email', 'surname', 'lastname', 'patronymic', 'dob', 'phone', 'language', 'blocking_date', 'blocking_reason'],
//            function ($query) use ($request) {
//                $query->leftjoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id');
//                $query ->where('student.blocked',1);
//                $query ->where('student.deleted',0);
//
//                self::makeStudentQuery($query,$request);
//
//            }
//        );


        if ($request->ajax() ) {

            return ['data' => $data,'data_block'=>[]];
        }

        $groupObj=[];
        if ($filter_id>0 && $request->filter =='group'){
            $groupObj=TeacherGroup::where('id',(int)$filter_id)->first();
            $groupObj['filterParam'] =$request->filter;
        }
        if ($filter_id>0 && $request->filter =='teacher'){
            $groupObj=Teacher::where('id',(int)$filter_id)->first();
            $groupObj['name']= $groupObj['surname'].' '.$groupObj['first_name'];
            $groupObj['filterParam'] =$request->filter;
        }
        if ($filter_id>0 && $request->filter =='franchisee'){
            $groupObj=Franchisee::where('id',(int)$filter_id)->first();
            $groupObj['name']= $groupObj['surname'].' '.$groupObj['first_name'];
            $groupObj['filterParam'] =$request->filter;
        }

        return view('admin.student.index', ['data' => $data,'data_block'=>[],'group_filter'=>$groupObj]);
    }

    public function MoneyLog(IndexMoneyLog $request) {



        if (!$this->AccessStudend($request->id)){
            return redirect('admin/students');
        }
        $student=Student::find($request->id);


        $data = AdminListing::create(GameBalanceLog::class)
            ->attachOrdering($request->sortBy, $request->sortDirection)
            ->processRequestAndGet($request,
            // set columns to query
            ['id', 'dates', 'coins', 'coins_before', 'coins_after', 'diams', 'diams_before', 'diams_after', 'comments'],
            // set columns to searchIn
            ['dates'],

            function ($query) use ($request) {
                if (!isset($request->orderDirection)){
                    $query ->orderBy('dates','desc');
                }
                $query ->where('student_id',$request->id);

            //    self::makeStudentQuery($query,$request);
            }
        );

        if ($request->ajax()) {
            return ['data' => $data];
        }

        $groupObj=[];

        return view('admin.student.money-log', ['data' => $data,'group_filter'=>$groupObj,'student'=>$student]);
    }
    private function makeStudentQuery($query, $request) {

        if   ((!empty($request->filter_id) && (int)$request->filter_id>0) && $request->filter =='group'){
            $query ->where('student.group_id',(int)$request->filter_id);
        }
        if   ((!empty(  $request->filter_id) && $request->filter_id>0) && $request->filter =='teacher'){
            $query ->where('student.teacher_id',(int)$request->filter_id);
        }
        if   ((!empty(  $request->filter_id) && $request->filter_id>0) && $request->filter =='franchisee'){
            $query ->where('student.franchisee_id',(int)$request->filter_id);
        }
        //--- Role Filter !!! important
        if   ((!empty(  $request->franchisee_id) && $request->franchisee_id>0)){
            $query ->where('student.franchisee_id',(int)$request->franchisee_id);
        }
        if   ((!empty(  $request->teacher_id) && $request->teacher_id>0)){
            $query ->where('student.teacher_id',(int)$request->teacher_id);
        }

    }


    public function indexBlocking(IndexStudent $request)
    {
        $role=session('role');
        if (empty($role)) {
            return redirect('admin/logout');
            die('Error role');
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

        return view('admin.student.index', ['data_block'=>$data_block]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create($group_id=0) {
        $group_id=(int)$group_id;
        $role=session('role');
        if (empty($role)) {
            return redirect('admin/logout');
            die('Error role');
        }
        $default_sudent=['franchisee'=>[],'group'=>[],'teacher'=>[],'type_payment'=>'offline','language'=>'uk'];
        $FranchiseeList=[];
        $default_sudent['start_day_group']=date('d-m-Y');
        if (session('role')=='admin') {
            $FranchiseeList=Franchisee::select('id','first_name','surname')->where('deleted',0)->get();
            if ($group_id>0){
                $teacheGroup=TeacherGroup::find($group_id);
                if (isset($teacheGroup)){
                    //  $default_sudent['start_day_group']=$teacheGroup->start_day;
                    $default_sudent['franchisee']=Franchisee::find($teacheGroup->franchisee_id);
                    $default_sudent['teacher']=Teacher::find($teacheGroup->teacher_id);
                    $default_sudent['groupSet']=$teacheGroup;
                }
            }
        }

        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
            if ($group_id>0){

                $teacheGroup=TeacherGroup::select('id','franchisee_id','teacher_id','name')->where('franchisee_id',$franchisee_id)->first();
                if (isset($teacheGroup)){

                    $default_sudent['franchisee']=Franchisee::find($teacheGroup->franchisee_id);
                    $default_sudent['teacher']=Teacher::find($teacheGroup->teacher_id);
                    $default_sudent['groupSet']=$teacheGroup;
                }
            }
            $FranchiseeList=Franchisee::select('id','first_name','surname')->where('deleted',0)->where('id',$franchisee_id)->get();
            $default_sudent['franchisee']=Franchisee::find($franchisee_id);
            $default_sudent['franchisee_id']=$franchisee_id;

        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id=Auth::guard('teacher')->user()->id;
            $franchisee_id=Auth::guard('teacher')->user()->franchisee_id;
            if ($group_id>0){
                $teacheGroup=TeacherGroup::select('id','franchisee_id','teacher_id','name')->where('id',$group_id)->where('franchisee_id',$franchisee_id)->where('teacher_id',$teacher_id)->first();

                if (isset($teacheGroup)){
                    $default_sudent['franchisee']=Franchisee::find($franchisee_id);

                    $default_sudent['groupSet']=$teacheGroup;
                    $default_sudent['group_id']=$group_id;
                }
            }
            $FranchiseeList=Franchisee::select('id','first_name','surname')->where('deleted',0)->where('id',$franchisee_id)->get();
            $default_sudent['franchisee']=Franchisee::find($franchisee_id);
            $default_sudent['franchisee_id']=$franchisee_id;

            $default_sudent['teacher']=Teacher::find($teacher_id);
            $default_sudent['teacher_id']=$teacher_id;
        }

        //------------- Role teacher \end


            $ipCountryCode=SiteHelper::GetIPInfo();
            $default_sudent['phone_country']=$ipCountryCode;
            $default_sudent['parent2_phone_country']=$ipCountryCode;
            $default_sudent['parent1_phone_country']=$ipCountryCode;



        $Discount=PaymentPeriod::get();
        return view('admin.student.create',['Franchisee' => $FranchiseeList,'Discount'=>$Discount,'student_def'=>collect($default_sudent)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStudent $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStudent $request)
    {
        $role=session('role');
        if (empty($role)) {
            die('Error role');
        }
        // Sanitize input
        $sanitized = $request->getSanitized();

        $sanitized['franchisee_id'] =(int) $request->getFranchiseeId();
        $sanitized['teacher_id'] =(int) $request->getTeacherId();
        $sanitized['group_id'] =(int) $request->getGroupId();
        $sanitized['discount'] =(int) $request->getDiscountId();
        $sanitized['password'] = Hash::make($sanitized['password']);
        $sanitized['enabled'] = 1;
        $sanitized['twochildren_id'] = $request->getChildrenId();

        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');
        $sanitized['parent1_phone'] = $request->getPhone('phone1Save');
        $sanitized['parent1_phone_country'] = $request->getPhoneCountry('phone2Save');

        $sanitized['parent2_phone'] = $request->getPhone('phone2Save');
        $sanitized['parent2_phone_country'] = $request->getPhoneCountry('phone2Save');

        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =(int)$franchisee_id;
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =(int)$teacher_id;
            $sanitized['franchisee_id'] = (int) $franchisee_id ;
        }
        //------------- Role teacher \end
        if (empty( $sanitized['is_twochildren'])){
            $sanitized['is_twochildren']=0;
            $sanitized['twochildren_id']=0;
            $sanitized['discount']=0;
        }else{
            $sanitized['discount']=10;
        }

        $discount=0;
        $price=0;
        if ($sanitized['franchisee_id']>0){
            $FranchiseeCurrent=Franchisee::select('id','fin_price_aboniment','fin_currency')->where('id',(int)$sanitized['franchisee_id'])->where('deleted',0)->first();
            if (isset($FranchiseeCurrent)){
                $price=$FranchiseeCurrent['fin_price_aboniment'];
                $sanitized['sum_aboniment'] =$FranchiseeCurrent['fin_price_aboniment'];
            }

        }
        if (!empty($sanitized['discount'])){
            $discount=(int)$price*(int)$sanitized['discount']/100;
        }else{
            $sanitized['discount']=0;
        }


        if (!empty($sanitized['PayDiscount']['id'])){
            $PaymentPeriodObj=PaymentPeriod::find($sanitized['PayDiscount']['id']);
            if (isset($PaymentPeriodObj->discount) && $PaymentPeriodObj->discount>0){
                $sanitized['sum_aboniment'] = $price;
            }

        }else{
            $sanitized['sum_aboniment'] = $price-$discount;
        }

        if (isset($PaymentPeriodObj->term)){
            if ($PaymentPeriodObj->term=='+12 months'){
                $sanitized['sum_aboniment'] = $price;
                $sanitized['discount']=0;
            }
            $str=date("Y-m-d", strtotime($sanitized['PayDate'])) . ' '.$PaymentPeriodObj->term.'';
            $savePayment['date_finish']=date("Y-m-d",strtotime("$str"));
        }

        // Normalize language to database format (UA -> uk, PL -> pl, EN -> en)
        if (isset($sanitized['language'])) {
            $sanitized['language'] = LocaleService::normalize($sanitized['language']) ?? LocaleService::getDefault();
        }

        // Store the Student
        $student = Student::create($sanitized);
        //----- Запись первой оплаты
        //if (!empty($PaymentPeriodObj->id) && $sanitized['type_payment']=='offline' || $sanitized['type_payment']=='online'){
        if (!empty($PaymentPeriodObj->id) && ($sanitized['type_payment'] == 'offline' || $sanitized['type_payment']=='online')){
            $discount_term=(int)$PaymentPeriodObj->discount;
            $sumPay=$sanitized['sum_aboniment']-round($sanitized['sum_aboniment']*$discount_term/100);
            $StudentPayment=new StudentPayment();
            $savePayment['student_id']=$student->id;
            $savePayment['aboniment_id']=$PaymentPeriodObj->id;
            $savePayment['date_pay']=date('Y-m-d',strtotime($sanitized['PayDate']));
            $savePayment['type_pay']='offline';
            $savePayment['discount']=$discount_term;
            if (isset($sanitized['PayComment'])){
                $savePayment['comment']=$sanitized['PayComment'];
            }

            $savePayment['sum_payment']=$sumPay;
            $savePayment['sum_aboniment']=$student->sum_aboniment;
            $StudentPayment->create($savePayment);
        }

        //---- Записать студенду домашеннее ТЗ
        try {
            \App\Models\StudentGroupTask::AssignStudentTask($student->id, (int)$sanitized['group_id']);
        } catch (\Throwable $e) {
            \Log::warning('Failed to assign student task: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return ['redirect' => url('admin/students'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/students');
    }

    /**
     * Display the specified resource.
     *
     * @param Student $student
     * @throws AuthorizationException
     * @return void
     */
    public function show(Student $student)
    {
        // $this->authorize('admin.student.show', $student);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Student $student
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Request $request, $id)
    {
        $role=session('role');
        if (empty($role)) {
            die('Error role');
        }
        $student = Student::findOrFail((int)$id);
        $student->load('Franchisee');
        $student->load('Teacher');

        if ($student->id>0){
            PlayerService::GetRand($student->id);
        }


        $discountPrice=[];
        $FranchiseeList=Franchisee::select('id','first_name','surname','fin_price_aboniment','fin_currency')->where('deleted',0)->get();

        if (session('role')=='admin'){
            if ($student->twochildren_id>0){
                $twochildren=Student::select('id','surname','lastname','patronymic')->where('deleted',0)->where('id',$student->twochildren_id)->get();
                $student->twochildren=$twochildren;

            }
        }

        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;
            $FranchiseeList=Franchisee::select('id','first_name','surname','fin_price_aboniment','fin_currency')->where('deleted',0)->where('id',$franchisee_id)->get();
            if ($student->twochildren_id>0){
                $twochildren=Student::select('id','surname','lastname','patronymic')->where('deleted',0)->where('id',$student->twochildren_id)->where('franchisee_id',$franchisee_id)->get();
                $student->twochildren=$twochildren;

            }
        }
        //------------- Role franchisee \end
        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            $FranchiseeList=Franchisee::select('id','first_name','surname','fin_price_aboniment','fin_currency')->where('deleted',0)->where('id',$franchisee_id)->get();
            if ($student->twochildren_id>0){
                $twochildren=Student::select('id','surname','lastname','patronymic')->where('deleted',0)->where('id',$student->twochildren_id)->where('franchisee_id',$franchisee_id)->where('teacher_id',$teacher_id)->get();
                $student->twochildren=$twochildren;
            }
        }
        //------------- Role teacher \end



        if (isset($student) && $student->id>0){
            $teacheGroup=TeacherGroup::select('id','franchisee_id','teacher_id','name')->where('id',$student->group_id)->first();
            if (isset($teacheGroup)){
                $student->groupSet=$teacheGroup;
            }
        }

        $data = $this->PaymentTable($request, $student->id);

        $Discount=PaymentPeriod::get();


        $student->fin_price_aboniment_discount=$student->sum_aboniment;

        $student->start_day_group=date('d-m-Y',strtotime($student->created_at));

        if (empty($student->phone_country) ||  empty($student->parent2_phone_country) ||   empty($student->parent1_phone_country)){
            $ipCountryCode=SiteHelper::GetIPInfo();
            if (empty($student->phone_country)){
                $student->phone_country=$ipCountryCode;
            }
            if (empty($student->parent2_phone_country)){
                $student->parent2_phone_country=$ipCountryCode;
            }
            if (empty($student->parent1_phone_country)){
                $student->parent1_phone_country=$ipCountryCode;
            }
        }
        // Language transformation handled by model accessor
        return view('admin.student.edit', ['student' => $student,'Franchisee' =>$FranchiseeList,'Discount'=>$Discount,'data'=>$data]);
    }

    //---------
     private function PaymentTable(Request $request,$studentId){

         $studentId=(int)$studentId;
            $data = AdminListing::create(StudentPayment::class)->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query  'surname', 'lastname', 'patronymic',  'teacher_groups.name as teacher',
                ['id', 'student.surname','student.lastname', 'date_pay', 'date_finish', 'sum_aboniment', 'payment_period.name as type_aboniment', 'type_pay', 'enabled'],

                // set columns to searchIn
                ['id', 'type_aboniment', 'type_pay'],

                function ($query) use ($request,$studentId) {
                 $query ->where('student_id',(int)$studentId);
                    $query->leftjoin('student', 'student.id', '=', 'student_payment.student_id');
                    $query->leftjoin('payment_period', 'payment_period.id', '=', 'student_payment.aboniment_id');

                }
            );

            if ($request->ajax()) {
                return  $data;
            }
            return  $data;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStudent $request
     * @param Student $student
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStudent $request, Student $student2,$id)
    {
        $role=session('role');
        if (empty($role)) {
            die('Error role');
        }
        // Sanitize input
        $sanitized = $request->getSanitized();

        $sanitized['franchisee_id'] =(int) $request->getFranchiseeId();
        $sanitized['teacher_id'] = (int)$request->getTeacherId();
        $sanitized['group_id'] = (int)$request->getGroupId();
        $sanitized['discount'] =(int)$request->getDiscountId();
        $sanitized['twochildren_id'] = (int)$request->getChildrenId();

        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');
        $sanitized['parent1_phone'] = $request->getPhone('phone1Save');
        $sanitized['parent1_phone_country'] = $request->getPhoneCountry('phone2Save');

        $sanitized['parent2_phone'] = $request->getPhone('phone2Save');
        $sanitized['parent2_phone_country'] = $request->getPhoneCountry('phone2Save');
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;

        }
        //------------- Role franchisee \end
        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
        }
        //------------- Role teacher \end


        if (empty( $sanitized['is_twochildren'])){
           $sanitized['is_twochildren']=0;
           $sanitized['twochildren_id']=0;
        }else{
            $sanitized['discount']=10;
        }

        if (empty( $sanitized['franchisee_id'])){
            unset( $sanitized['franchisee_id']);
        }
        if (empty( $sanitized['teacher_id'])){
            unset( $sanitized['teacher_id']);
        }
        if (empty($sanitized['password'])){
            unset($sanitized['password']);
        }else{
            $sanitized['password'] = Hash::make($sanitized['password']);
        }
        if (empty($sanitized['group_id'])){
            unset($sanitized['group_id']);
        }
        $discount=0;
        $price=0;
        if ($sanitized['franchisee_id']>0){
            $FranchiseeCurrent=Franchisee::select('id','fin_price_aboniment','fin_currency')->where('id',$sanitized['franchisee_id'])->where('deleted',0)->first();
           if (isset($FranchiseeCurrent)){
               $price=$FranchiseeCurrent['fin_price_aboniment'];
           }

        }
        if (!empty($sanitized['discount'])){
            $discount=(int)$price*(int)$sanitized['discount']/100;
        }else{
            $sanitized['discount']=0;
        }

        $sanitized['sum_aboniment'] = $price-$discount;


        $student=Student::find($id);
        //------ Смена группы
        if ($student->group_id!=$sanitized['group_id']){
            StudentTrainingTask::where('student_id',$student->id)->where('is_done',0)->delete();
            StudentGroupTask::AssignStudentTask($student->id,$sanitized['group_id']);
        }

        // Normalize language to database format (UA -> uk, PL -> pl, EN -> en)
        if (isset($sanitized['language'])) {
            $sanitized['language'] = LocaleService::normalize($sanitized['language']) ?? $student->language;
        }

        $student->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/students'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/students');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStudent $request
     * @param Student $student
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStudent $request, Student $student2)
    {
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher'])) {
            return redirect('/logout');
        }
        $student=$student2;

        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;
            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
            if ($student->teacher_id!=$teacher_id){
                return redirect()->back();
            }
        }
        //------------- Role teacher \end

        if (!empty($student->id)){
            $student->deleted=1;
            $student->save();
        }

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStudent $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStudent $request) : Response
    {
//        DB::transaction(static function () use ($request) {
//            collect($request->data['ids'])
//                ->chunk(1000)
//                ->each(static function ($bulkChunk) {
//                    Student::whereIn('id', $bulkChunk)->delete();
//
//                    // TODO your code goes here
//                });
//        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
    //------- Payment add
    public function payment(Request $request, Student $student2,$id) {

        $id=(int)$id;
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher'])) {
            return redirect('/logout');
        }
        $student=Student::find($id);
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;

            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
            if ($student->teacher_id!=$teacher_id){
                return redirect()->back();
            }
        }
        //------------- Role teacher \end

        if (!empty($student->id)){
            $params=json_decode($request->getContent(), true);

            if (isset($params['PayDiscount']['id']) && $params['PayDiscount']['id']>0){
                $PaymentPeriodObj=PaymentPeriod::find($params['PayDiscount']['id']);
            }
           $StudentPayment=new StudentPayment();
            $savePayment['student_id']=$student->id;

            if (isset($params['payment_date'])){
                $savePayment['date_pay']=date('Y-m-d',strtotime($params['payment_date']));
            }
            if (isset($PaymentPeriodObj->discount)){
                $savePayment['discount']=$PaymentPeriodObj->discount;
            }
            if (isset($PaymentPeriodObj->term)){
                if ($PaymentPeriodObj->discount>0){
                    $sum_aboniment=$student->Franchisee->fin_price_aboniment;
                    $savePayment['sum_aboniment'] = $sum_aboniment-($sum_aboniment*($PaymentPeriodObj->discount)/100);
                }else{
                    $savePayment['sum_aboniment'] = $student->sum_aboniment-($student->sum_aboniment*($PaymentPeriodObj->discount)/100);
                }
                $str=date("Y-m-d", strtotime($savePayment['date_pay'])) . ' '.$PaymentPeriodObj->term.'';
                $savePayment['date_finish']=date("Y-m-d",strtotime("$str"));
            }
            if (isset($params['comment'])){
                $savePayment['comment']=$params['comment'];
            }
            $savePayment['aboniment_id']=$PaymentPeriodObj->id;
            $StudentPayment->create($savePayment);
        }
        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    public function StatisticPrint($student_id){
        die('Что печатать , документ как выглядет');
    }
    //------- Блокировка ученика
    public function lock(Request $request,$id) {

        $id=(int)$id;
        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])) {
            return redirect('/logout');
        }
        $student=Student::find($id);
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;

            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();

            }
            if ($student->teacher_id!=$teacher_id){
                return redirect()->back();

            }
        }
        //------------- Role teacher \end

        if (!empty($student->id)){

            $blocking_reason=$request->input('blocking_reason','');

            $student->blocking_date=date('Y-m-d');
            $student->blocked=1;
            if (!empty($blocking_reason)){
                $student->blocking_reason=$request->get('blocking_reason','');
            }
            $student->save();
        }
        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }
        return redirect()->back();
    }
    //------- Раблокировка  ученика
    public function unlock(Request $request,$id) {
        $id=(int)$id;
        if (empty($id)){
            return redirect()->back();
        }

        $role=session('role');
        if (!in_array($role,['admin' ,'franchisee','teacher','manager'])) {
            die('not rigth');
        }
        $student=Student::find($id);
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;

            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            if ($student->franchisee_id!=$franchisee_id){
                return redirect()->back();
            }
            if ($student->teacher_id!=$teacher_id){
                return redirect()->back();
            }
        }
        //------------- Role teacher \end

        if (!empty($student->id)){

            $student->blocking_date=null;
            $student->blocked=0;
            $student->blocking_reason='';

            $student->save();
            if ($request->ajax()) {
                return response(['message' => trans('admin.operation.succeeded')]);
            }
        }else{
            return response(['message' => trans('admin.operation.error')]);
        }

        return redirect()->back();
    }
    public function GetTeacherByFrancisee($franchisee_id){
        //------------- Role franchisee /begin
        $franchisee_id=(int)$franchisee_id;
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;

        }
        //------------- Role teacher \end

        $franchisee_id=(int)$franchisee_id;
        $jsonRet=['teacher'=>[],'price'=>['fin_price_aboniment'=>0,'fin_currency'=>'UAH']];
        if ($franchisee_id>0){
            $jsonRet['teacher']=Teacher::where('franchisee_id',$franchisee_id)->select('id','surname','first_name')->where('deleted',0)->get()->toArray();
            $FranchiseeList=Franchisee::select('id','fin_price_aboniment','fin_currency')->where('id',$franchisee_id)->where('deleted',0)->first();
           if (isset($FranchiseeList)){
               $jsonRet['price']['fin_price_aboniment']=$FranchiseeList['fin_price_aboniment'];
               $jsonRet['price']['fin_currency']=$FranchiseeList['fin_currency'];
           }

        }
        if (empty($jsonRet)){
            $jsonRet=['teacher'=>[] ];
        }
        return response()->json($jsonRet);
    }
    public function GetChildren($franchisee_id,$id){
        $jsonRet=['children'=>[]];
        $id=(int)$id;
        $franchisee_id=(int)$franchisee_id;
        if (session('role')=='admin'){
            if ($franchisee_id>0){
                $jsonRet['children']=Student::where('franchisee_id',$franchisee_id)->select('id','surname','lastname','patronymic')->where('deleted',0)->where('id','<>',$id)->get()->toArray();
            }
        }
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            if ($franchisee_id>0){
                $jsonRet['children']=Student::where('franchisee_id',$franchisee_id)->select('id','surname','lastname','patronymic')->where('deleted',0)->where('id','<>',$id)->get()->toArray();
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            $sanitized['teacher_id'] =$teacher_id;
            $sanitized['franchisee_id'] =  $franchisee_id ;
            if ($franchisee_id>0){
                $jsonRet['children']=Student::where('franchisee_id',$franchisee_id)->where('teacher_id',$teacher_id)->select('id','surname','lastname','patronymic')->where('deleted',0)->where('id','<>',$id)->get()->toArray();
            }

        }
        //------------- Role teacher \end

        if (empty($jsonRet)){
            $jsonRet=['children'=>[] ];
        }
        return response()->json($jsonRet);
    }

    public function GetGroupByTeacher($teacher_id){
        $teacher_id=(int)$teacher_id;
        $jsonRet=['group'=>[]];
        if ($teacher_id>0){

            //------------- Role admin /begin
            if (session('role')=='admin' || session('role')=='manager'){
                $jsonRet['group']=TeacherGroup::where('teacher_id',$teacher_id)->select('id','name','start_day')->where('deleted',0)->get()->toArray();
            }
            //------------- Role admin \end

            //------------- Role franchisee /begin
            if (session('role')=='franchisee'){
                $franchisee_id =Auth::guard('franchisee')->user()->id;
                $jsonRet['group']=TeacherGroup::where('teacher_id',$teacher_id)->where('franchisee_id',$franchisee_id)->select('id','name','start_day')->where('deleted',0)->get()->toArray();
            }
            //------------- Role franchisee \end

            //------------- Role teacher /begin
            if (session('role')=='teacher') {
                $teacher_id = Auth::guard('teacher')->user()->id;
                $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
                $jsonRet['group']=TeacherGroup::where('teacher_id',$teacher_id)->where('franchisee_id',$franchisee_id)->where('teacher_id',$teacher_id)->select('id','name','start_day')->where('deleted',0)->get()->toArray();

            }
            //------------- Role teacher \end

        }
        if (empty($jsonRet)){
            $jsonRet=['group'=>[] ];
        }
        return response()->json($jsonRet);
    }
    private function GetNameWho(){
        if (session('role')=='franchisee'){
            $franchiseeObj =Auth::guard('franchisee')->user();
             $name=$franchiseeObj->first_name.' '.$franchiseeObj->surname;
        }
        if (session('role')=='teacher') {
            $teacherObj =Auth::guard('teacher')->user();
            $name=$teacherObj->first_name.' '.$teacherObj->surname;
        }
        if (session('role')=='managers') {
            $managersObj =Auth::guard('managers')->user();
            $name=$managersObj->first_name.' '.$teacherObj->surname;
        }
        if (session('role')=='admin') {
            $teacherObj =Auth::guard('admin')->user();
            $name=$teacherObj->first_name.' '.$teacherObj->surname;
        }
        return $name;
    }
    private function AccessStudend($id){
        $id=(int)$id;
        $student=Student::find($id);
        //------------- Role franchisee /begin
        if (session('role')=='franchisee'){
            $franchisee_id =Auth::guard('franchisee')->user()->id;
            $sanitized['franchisee_id'] =$franchisee_id;
            if ($student->franchisee_id!=$franchisee_id){
                return 0;
            }
        }
        //------------- Role franchisee \end

        //------------- Role teacher /begin
        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;

            if ($student->franchisee_id!=$franchisee_id){
                return 0;
            }
            if ($student->teacher_id!=$teacher_id){
                return 0;
            }
        }
        return 1;
    }

    public function changeBalanceCoins(Request $request,$id) {
        $id=(int)$id;
        if (!$this->AccessStudend($id)){
            return redirect()->back();
        }
        $isBalance =$request->get('get-balance',0);
        if ($isBalance){
            $ret=CoinService::GetBalance($id);
            $ret['message']='OK';
            $ret['student_id']=$id;
            return  response()->json($ret);
        }
        //------------- Role teacher \end
        $sum =$request->get('change_balance');
        $description =$request->get('description','');
        $whoAdd=$this->GetNameWho();
        CoinService::addCoins($id,$sum, $description.  ' (c)'.$whoAdd);
        $ret=CoinService::GetBalance($id);
        $ret['message']='OK';
        $ret['student_id']=$id;
        return  response()->json($ret);

    }

    public function changeBalanceDiams(Request $request,$id) {
        $id=(int)$id;
        if (!$this->AccessStudend($id)){
            return redirect()->back();
        }

        $isBalance =$request->get('get-balance',0);
        if ($isBalance){
            $ret=CoinService::GetBalance($id);
            $ret['message']='OK';
            $ret['student_id']=$id;
            return  response()->json($ret);
        }

        $sum =$request->get('change_balance');
        $sum=(int)$sum;
        $description =$request->get('description','');
        $whoAdd=$this->GetNameWho();
        CoinService::addDiams($id,$sum, $description. ' (c)'.$whoAdd);
        $ret=CoinService::GetBalance($id);
        $ret['message']='OK';
        $ret['student_id']=$id;
        return  response()->json($ret);
    }
    public function changeEmail(UpdateEmail $request,$id) {

        $id=(int)$id;
        $student =Student::find($id);
        $sanitized = $request->getSanitized();
        $response =(object)[];
        $studentFind =Student::where('email', $sanitized['change_email'])->first();
        $teacher =Teacher::where('email', $sanitized['change_email'])->first();
        $franchisee =Franchisee::where('email', $sanitized['change_email'])->first();
        if(($studentFind || $teacher || $franchisee)) {
            $response->messageExist =trans('admin.operation.existed');
            return  response()->json($response);
        }

        $uuid = Str::uuid()->toString();
        session()->put('uuidEmail', $uuid);
        $newEmail =(object)[];
        $newEmail->email =$sanitized['change_email'];
        $newEmail->uuid =$uuid;
        $newEmail->name =$student->first_name;
        $newEmail->link =urlencode('http://test:8080/admin/students/'.$id.'/update-email');

        Mail::to($newEmail->email)->send(new ChangeEmail($newEmail));

//так как у меня нет открытого айпишника, делаю это здесь

        $student->email =$sanitized['change_email'];
        $student->save();

        $response->newEmail =$student->email;
        $response->messageSuccess =trans('admin.operation.succeeded');

        return  response()->json($response);

        dd($request->all(), $newEmail);
    }


    //если бы у меня был открытый айпишник и я бы мог по ссылке попасть сюда
    public function updateEmail(Request $request,$id) {
//
//        $id=(int)$id;
//        $uuid =$request->get('uuid');
//        $email =$request->get('email');
//
//        if (session('uuidEmail')===$uuid){
//            $student =Student::find($id);
//            $student->email =$email;
//            $student->save();
//            $response =(object)[];
//            $response->newEmail =$student->email;
//            $response->messageSuccess ="Email Changed";
//
//            return  response()->json($response);
//        }
    }

    public function changePassword(UpdatePassword $request,$id) {

//        $id=(int)$id;
//        $sanitized = $request->getSanitized();
//        $response =(object)[];
//        $student =Student::find($id);
//        if($student) {
//            $uuid = Str::uuid()->toString();
//            session()->put('uuidPassword', $uuid);
//            $newPassword = (object)[];
//            $newPassword->email = $student->email;
//            $newPassword->name = $student->first_name;
//            $newPassword->password = $sanitized['change_password'];
//            $newPassword->uuid =$uuid;
//            $newPassword->link =urlencode('http://test:8080/admin/students/'.$id.'/update-password');
//            Mail::to($newPassword->email)->send(new ChangeEmail($newPassword));
//
//            //так как у меня нет открытого айпишника, делаю это здесь
//            $student->password =Hash::make($sanitized['change_password']);
//            $student->save();
//            $response->messageSuccess =trans('admin.operation.succeeded');
//
//            return  response()->json($response);
//        }
//        else{
//            $response->messageError ="Error";
//            return  response()->json($response);
//        }
    }

    //если бы у меня был открытый айпишник и я бы мог по ссылке попасть сюда
    public function updatePassword(Request $request,$id) {

//        $id=(int)$id;
//        $uuid =$request->get('uuid');
//        $password =$request->get('password');
//        $student =Student::find($id);
//        if (session('uuidPassword')===$uuid){
//            $student =Student::find($id);
//            $student->password =Hash::make($password);
//            $student->save();
//
//            $response =(object)[];
//            $response->messageSuccess ="Password Changed";
//
//            return  response()->json($response);
//        }

    }
    public function printXls() {

        if (!(session('role')=='franchisee' || session('role')=='admin')){
            die('No access');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers[]=['label'=>trans('admin.student.columns.fio'),'field'=>'fio','width'=>25];
        $headers[]=['label'=>trans('admin.student.columns.start_day') ,'field'=>'created_at','width'=>15];
        $headers[]=['label'=>trans('admin.student.columns.teacher_id'),'field'=>'teacher_id','width'=>30];
        $headers[]=['label'=>trans('admin.student.columns.group_id'),'field'=>'group_name','width'=>30];

        $headers[]=['label'=>trans('admin.student.columns.phone'),'field'=>'phone','width'=>25];
        $headers[]=['label'=>trans('admin.student.columns.email'),'field'=>'email','width'=>25];
        $headers[]=['label'=>trans('admin.student.columns.dob'),'field'=>'dob','width'=>15];
        $headers[]=['label'=> trans('admin.student.columns.sum_aboniment'),'field'=>'sum_aboniment','width'=>12];
        $headers[]=['label'=> trans('admin.student.columns.discount'),'field'=>'discount','width'=>10];
        $headers[]=['label'=> trans('admin.student.columns.balance'),'field'=>'balance','width'=>8];
        $headers[]=['label'=> trans('admin.student.columns.diams'),'field'=>'diams','width'=>8];
        $headers[]=['label'=> trans('admin.student.columns.blocked'),'field'=>'blocked','width'=>8];
        $headers[]=['label'=> trans('admin.student.columns.blocking_date'),'field'=>'blocking_date','width'=>12];
        $headers[]=['label'=> trans('admin.student.columns.blocking_reason'),'field'=>'blocking_reason','width'=>30];
        $headers[]=['label'=> trans('admin.student.columns.last_login_at'),'field'=>'last_login_at','width'=>12];
        $colIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $header['label']);
            $sheet->getColumnDimensionByColumn($colIndex)->setWidth($header['width']);
            $colIndex++;
        }

        $sheet->getStyleByColumnAndRow(1, 1, count($headers), 1)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFC9DAF8');
        $sheet->getStyleByColumnAndRow(1, 1, count($headers), 1)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $row = 2;
        $studentObj= Student::query()
            ->leftJoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id')
            ->leftJoin('teacher', 'teacher.id', '=', 'student.teacher_id')
            ->where('student.deleted', 0);
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
            $studentObj->where('student.franchisee_id','=',$franchisee_id);
        }
        $studentObj->orderBy('student.surname', 'asc')
            ->select('student.id', 'student.surname', 'student.lastname', 'student.patronymic',
                'student.phone', 'student.email', 'student.balance', 'student.blocked',
                'student.created_at', 'student.last_login_at', 'student.blocking_date', 'student.dob',
                'student.teacher_id', 'teacher_groups.name as group_name',
                'teacher.surname as teacher_surname', 'teacher.first_name as teacher_firstname');
        $studentObj->chunk(500, function ($students) use (&$row, $sheet, $headers) {
            foreach ($students->toArray() as $student) {
                $colIndex = 1;
                foreach ($headers as $fields){
                    $setValue= $student[$fields['field']] ?? '';
                    if ($fields['field']=='fio'){
                        $setValue=  ($student['surname'] ?? '') . ' ' . ($student['lastname'] ?? '')  ;
                    }
                    if ($fields['field']=='created_at' || $fields['field']=='last_login_at' || $fields['field']=='blocking_date' || $fields['field']=='dob'){

                        $setValue= date('d-m-Y',strtotime($student[$fields['field']] ?? '')) ;
                        if ($setValue=='01-01-1970'){
                            $setValue='' ;
                        }

                    }
                    if ($fields['field']=='phone'){
                        $setValue=' '.($student['phone'] ?? '') ;
                    }
                    if ($fields['field']=='blocked'){
                        if (empty($student['blocked'])){
                            $setValue='' ;
                        }else{
                            $setValue='X' ;
                        }

                    }
                    if ($fields['field']=='teacher_id'){
                        $setValue=  ($student['teacher_surname'] ?? '') . ' ' . ($student['teacher_firstname'] ?? '')  ;
                    }

                    $sheet->setCellValueByColumnAndRow($colIndex, $row,$setValue);
                    $colIndex++;
                }
                $row++;
            }
        });
        $lastRow = $row - 1; // $row увеличивается после последней строки
        $lastCol = chr(ord('A') + count($headers) - 1); // Например, 'K' для 11 столбцов

        $cellRange = "A1:{$lastCol}{$lastRow}";

        $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        $sheet->getStyle($cellRange)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $writer = new Xls($spreadsheet);
        $fileName = "Student-list_".date('d_m_Y hi').".xls";
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_clean();

        return \Illuminate\Support\Facades\Response::make($xlsData, 200, ['Content-Type' => 'application/vnd.ms-excel', 'Content-Disposition' => "attachment; filename=\"$fileName\""]);

    }
}
