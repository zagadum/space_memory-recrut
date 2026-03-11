<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Helpers\TraningHelper;
use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use App\Models\OlympiadResult;
use App\Models\Student;
use App\Models\StudentGroupTask;
use App\Models\Teacher;
use App\Models\TeacherGroup;
use App\Models\StudentTrainingTask;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
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
    //--- Роутер распределяет календари
    public function calendarIndex()
    {
        $franchisee_id=0;
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
        }
        if (session('role')=='teacher'){
            //Переадресация на календарь учителя.
            $teacher_id = Auth::guard('teacher')->user()->id;
            //$franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
            return redirect('/admin/calendar/teacher/'.$teacher_id);
        }
        if (session('role')=='admin' or session('role')=='manager') {
            $franchiseeList = Franchisee::select('id', 'surname', 'first_name')->where('deleted', 0)->where('enabled', 1)->first();
            $franchisee_id=$franchiseeList->id;
        }
        return $this->franchiseesIndex($franchisee_id);
    }

    //---- Календарь Франшизе
    public function franchiseesIndex($franchisee_id=0) {
        $franchisee_id=(int)$franchisee_id;
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
        }
        if (session('role')=='teacher'){
            $teacher_id = Auth::guard('teacher')->user()->id;
            return redirect('/admin/calendar/teacher/'.$teacher_id);
        }

        $franchiseeBreadCrumb =Franchisee::where('id',(int)$franchisee_id)->first();
        $franchisee = Franchisee::select('id', 'surname', 'first_name')->where('deleted',0)->where('enabled',1)->get();
        $info = collect(['franchisee'=>$franchiseeBreadCrumb,'franchisee_id'=>$franchisee_id]);
        $TeacherCnt = Teacher::where('franchisee_id',$franchisee_id)->where('enabled',1)->count();
        return view('admin.calendar.franchisees', ['franchisee' => $franchisee,'info'=>$info,'TeacherCnt'=>$TeacherCnt, 'franchisee_id'=>$franchisee_id, 'franchiseeBreadCrumb'=>$franchiseeBreadCrumb]);
    }

    //---- Календарь Груп
    public function groupsIndex($group_id=0){
        $group_id=(int)$group_id;
        $groupBreadCrumb =TeacherGroup::where('id',(int)$group_id)->first();
        $teacherBreadCrumb =Teacher::where('id',(int)$groupBreadCrumb->teacher_id)->first();
        $franchiseeBreadCrumb =Franchisee::where('id',(int)$teacherBreadCrumb->franchisee_id)->first();
       // dd($teacherBreadCrumb);
        return view('admin.calendar.groups', ['group_id'=>$group_id, 'franchiseeBreadCrumb'=>$franchiseeBreadCrumb,'teacherBreadCrumb'=>$teacherBreadCrumb, 'groupBreadCrumb'=>$groupBreadCrumb]);
    }

    //---- Календарь Учителя
    public function teachersIndex($teacher_id=0){
        $teacher_id=(int)$teacher_id;
        $teacherBreadCrumb =Teacher::where('id',(int)$teacher_id)->first();

        $franchiseeBreadCrumb =Franchisee::where('id',(int)$teacherBreadCrumb->franchisee_id)->first();

        return view('admin.calendar.teachers', ['teacher_id'=>$teacher_id, 'franchiseeBreadCrumb'=>$franchiseeBreadCrumb,'teacherBreadCrumb'=>$teacherBreadCrumb]);
    }
    public function studentsIndex($student_id=0){
        $student_id=(int)$student_id;
        if ($student_id>0){
            $studentBreadCrumb =Student::where('id',(int)$student_id)->first();
            if (empty($studentBreadCrumb)){
                return ['redirect' => url('admin/calendar')];
            }
            $groupBreadCrumb =TeacherGroup::where('id',(int)$studentBreadCrumb['group_id'])->first();
            $teacherBreadCrumb =Teacher::where('id',(int)$groupBreadCrumb['teacher_id'])->first();

        }

        $franchiseeBreadCrumb =Franchisee::where('id',(int)$studentBreadCrumb->franchisee_id)->first();
        return view('admin.calendar.students', ['student_id'=>$student_id,'franchiseeBreadCrumb'=>$franchiseeBreadCrumb,'teacherBreadCrumb'=>$teacherBreadCrumb, 'groupBreadCrumb'=>$groupBreadCrumb, 'studentBreadCrumb'=>$studentBreadCrumb]);
    }

    public function GetEventFranchisees(Request $request, $franchisee_id=0) {
        $franchisee_id=(int)$franchisee_id;
        if (!in_array(session('role'),['franchisee','admin','manager'])){
             return response()->json(['not access']);
        }
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
        }

        $start=$request->get('start');
        $end=$request->get('end');

        if (!$start || !strtotime($start)) {
            $start = date('Y-m-d');
        }

        if (!$end || !strtotime($end)) {
            $end = date('Y-m-d', strtotime('+1 month'));
        }
        //Выбираем учителей
       $CalendarTeacherObj=TeacherGroup::where('teacher_groups.deleted',0)->where('teacher_groups.enabled',1)
           ->select('teacher.surname','teacher.first_name',
               'teacher_groups.teacher_id',
               'teacher_groups.franchisee_id',
               'teacher_groups.start_day','teacher_groups.workday1','teacher_groups.workday2','teacher_groups.workday3','teacher_groups.workday4','teacher_groups.workday5','teacher_groups.workday6','teacher_groups.workday7')
           ->join('teacher', function ($join) {
               $join->on('teacher_groups.teacher_id', '=', 'teacher.id');
               $join->where('teacher.enabled',1)->where('teacher.deleted',0);
           });
       if ($franchisee_id>0){
           $CalendarTeacherObj->where('teacher.franchisee_id',$franchisee_id);
       }
       $CalendarTeacher=$CalendarTeacherObj->distinct()->get();

       //dd($CalendarTeacher);

        $json_data = [];
        if (empty($start)){
            $start='01'.date('-m-Y');
        }
        if (empty($end)){
            $end='31'.date('-m-Y');
        }
        $start_date=date('d-m-Y',strtotime($start));
        $dayEnd=date('d-m-Y',strtotime($end));


       // $start_day = date('z', strtotime($start_date)); // 6th of June
        //$days_in_a_year = date('z', strtotime($dayEnd)); // 31th of december
        //$number_of_days = ($days_in_a_year - $start_day) +1 ; // Add the last of december also
        $datetime1 = date_create($start_date);
        $datetime2 = date_create($dayEnd);
        $interval = date_diff($datetime1, $datetime2);


        $number_of_days= $interval->format('%a');
//        $json_data['test']['start_date']=$start_date;
//        $json_data['test']['dayEnd']=$dayEnd;
//        $json_data['test']['number_of_days']=$number_of_days;
//
        foreach ($CalendarTeacher as $TeacherByGroup){
           $nameTeacher=$TeacherByGroup->surname.' '.$TeacherByGroup->first_name;
           $EventOne['title']=$nameTeacher;
           $EventOne['teacher_id']=$TeacherByGroup->teacher_id;
           $EventOne['franchisee_id']=$TeacherByGroup->franchisee_id;
           $EventOne['dateStart']=$TeacherByGroup->start_day;
            $EventOne['textColor'] ='#778192';
           //--Перебераем текущий месяц
           for ($i = 0; $i < $number_of_days; $i++) {
               $date = strtotime(date("Y-m-d", strtotime($start_date)) . " +$i day");
               $dayNumber=date('N', $date);
               if ( ($dayNumber==1 && $TeacherByGroup->workday1==1)
                   || ($dayNumber==2 && $TeacherByGroup->workday2==1)
                   || ($dayNumber==3 && $TeacherByGroup->workday3==1)
                   || ($dayNumber==4 && $TeacherByGroup->workday4==1)
                   || ($dayNumber==5 && $TeacherByGroup->workday5==1)
                   || ($dayNumber==6 && $TeacherByGroup->workday6==1)
                   || ($dayNumber==7 && $TeacherByGroup->workday7==1)
               ){
                   $EventOne['start']=date('Y-m-d',$date);
                   $EventOne['end']=date('Y-m-d',$date);
                   $EventOne['color']='white';
                   $EventOne['url']='/admin/calendar/teacher/'.$TeacherByGroup->teacher_id;
                   $json_data[]=$EventOne;
               }

               //echo date('d.m.Y N - l', $date) .'<br />';
           }

       }
        $exitKey=[];
        $json_dataFilter=[];
        foreach ($json_data as $value){
            $key=md5($value['start'].$value['teacher_id']);
            if (in_array($key,$exitKey)){
                continue;
            }
            $exitKey[]=$key;
            $json_dataFilter[]=$value;
        }


//dd($json_data);
        return response()->json($json_dataFilter);
    }

    public function GetEventTeacher(Request $request){
        $franchisee_id=0;
        if (!in_array(session('role'),['franchisee','admin','teacher','manager'])){
            return response()->json(['not access']);
        }
        if (session('role')=='franchisee'){
            $franchisee_id=Auth::guard('franchisee')->user()->id;
        }
        $start=$request->get('start');
        $end=$request->get('end');

        if (!$start || !strtotime($start)) {
            $start = date('Y-m-d');
        }

        if (!$end || !strtotime($end)) {
            $end = date('Y-m-d', strtotime('+1 month'));
        }

        if (session('role')=='teacher') {
            $teacher_id = Auth::guard('teacher')->user()->id;
            $franchisee_id = Auth::guard('teacher')->user()->franchisee_id;
        }else{
            $teacher_id=$request->get('teacher_id',1);
        }

        //Выбираем учителей
        $CalendarTeacherObj=TeacherGroup::where('teacher_groups.deleted',0)->where('teacher_groups.enabled',1)
            ->select(
                'teacher_groups.id',
                'teacher_groups.name as group_name',
                'teacher_groups.teacher_id',
                'teacher_groups.franchisee_id',
                'teacher_groups.start_day','teacher_groups.workday1','teacher_groups.workday2','teacher_groups.workday3','teacher_groups.workday4','teacher_groups.workday5','teacher_groups.workday6','teacher_groups.workday7');


        if ($teacher_id>0){
            $CalendarTeacherObj->where('teacher_groups.teacher_id',$teacher_id);
        }
        if ($franchisee_id>0){
            $CalendarTeacherObj->where('teacher_groups.franchisee_id',$franchisee_id);
        }
        $CalendarTeacher=$CalendarTeacherObj->distinct()->get();

        $json_data = [];
        if (empty($start)){
            $start='01'.date('-m-Y');
        }
        if (empty($end)){
            $end='31'.date('-m-Y');
        }
        $start_date=date('d-m-Y',strtotime($start));
        $dayEnd=date('d-m-Y',strtotime($end));

        $datetime1 = date_create($start_date);
        $datetime2 = date_create($dayEnd);
        $interval = date_diff($datetime1, $datetime2);


        $number_of_days= $interval->format('%a');

        foreach ($CalendarTeacher as $TeacherByGroup){
            $nameTeacher=$TeacherByGroup->group_name;
            $EventOne['title']=$nameTeacher;
            $EventOne['teacher_id']=$TeacherByGroup->teacher_id;
            $EventOne['franchisee_id']=$TeacherByGroup->franchisee_id;
            $EventOne['dateStart']=$TeacherByGroup->start_day;
            $EventOne['textColor'] ='#778192';
            //--Перебераем текущий месяц
            for ($i = 0; $i < $number_of_days; $i++) {
                $date = strtotime(date("Y-m-d", strtotime($start_date)) . " +$i day");
                $dayNumber=date('N', $date);
                if ( ($dayNumber==1 && $TeacherByGroup->workday1==1)
                    || ($dayNumber==2 && $TeacherByGroup->workday2==1)
                    || ($dayNumber==3 && $TeacherByGroup->workday3==1)
                    || ($dayNumber==4 && $TeacherByGroup->workday4==1)
                    || ($dayNumber==5 && $TeacherByGroup->workday5==1)
                    || ($dayNumber==6 && $TeacherByGroup->workday6==1)
                    || ($dayNumber==7 && $TeacherByGroup->workday7==1)
                ){
                    $EventOne['start']=date('Y-m-d',$date);
                    $EventOne['end']=date('Y-m-d',$date);
                    $EventOne['color']='white';

                    $EventOne['url']='/admin/calendar/groups/'.$TeacherByGroup->id;
                    $json_data[]=$EventOne;
                }

            }
        }

        return response()->json($json_data);
    }
    public function GetEventGroup(Request $request){
        $start=$request->get('start');
        $end=$request->get('end');
        $group_id=$request->get('group_id',1);
        $group_id=(int)$group_id;


        if (empty($start)){
            $start='01'.date('-m-Y');
        }
        if (empty($end)){
            $end='31'.date('-m-Y');
        }
        $start_date=date('Y-m-d',strtotime($start));
        $dayEnd=date('Y-m-d 23:59:59',strtotime($end));
        $CalendarGroup=[];$json_data = [];
//        //Выбираем cтуденов по
//->where('student.deleted',0)
        //->where('student.blocked',0)
        $CalendarGroupObj=StudentGroupTask::where('student_group_task.enabled',1)
         ->leftjoin('student', 'student_group_task.group_id', '=', 'student.group_id')
        ->select('student_group_task.id as task_id','student_group_task.date_start','student.id as student_id','student.surname','student.lastname','student_group_task.group_id');
        $CalendarGroupObj->whereBetween('date_start',[$start_date,$dayEnd]);
        if ($group_id>0){
            $CalendarGroupObj->where('student_group_task.group_id',$group_id);
        }
        $CalendarGroupObj->where('student.blocked',0);
        $CalendarGroupObj->where('student.deleted',0);
        $CalendarGroupMaster=$CalendarGroupObj->distinct()->get();
        //$sql = $CalendarGroupObj->toSql();
        //$bindings = $CalendarGroupObj->getBindings();
       //print_r($bindings);
        //dd($sql);
        foreach ($CalendarGroupMaster as $ByGroup){

            $CalendarGroup[$ByGroup->date_start][$ByGroup->student_id]=$ByGroup;
        }

        $CalendarTaskObj=StudentTrainingTask::where('student.deleted',0)->where('student.blocked',0)->where('is_self',0)
            ->leftjoin('student', 'student_training_task.student_id', '=', 'student.id')
            ->select('student_training_task.id as task_id','student_training_task.date_start','student.id as student_id','student.surname','student.lastname','student_training_task.group_id as link_group_id','is_done');
        $CalendarTaskObj->whereBetween('date_start',[$start_date,$dayEnd]);
        if ($group_id>0){
            $CalendarTaskObj->where('student.group_id',$group_id);
        }

        $CalendarTaskList=$CalendarTaskObj->distinct()->get();
           //print $sql;

        // $bindings = $CalendarTaskList->getBindings();
      //  dd($sql, $bindings);

        $CalendarGroupStatus=[];
        $isDoneMark=[];
        foreach ($CalendarTaskList as $ByGroup) {
            if ($ByGroup->is_done == 1) {
                $isDoneMark[$ByGroup->student_id][] = $ByGroup->date_start;
            } elseif ($ByGroup->is_done == 0) {
                $CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id]['is_done'] = 0;
            }
            if ($ByGroup->is_done == 2) {
                $CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id]['is_done'] = 2;
            }

            if (empty($CalendarGroup[$ByGroup->date_start][$ByGroup->student_id]) && empty($ByGroup->link_group_id)) {
                $CalendarGroup[$ByGroup->date_start][$ByGroup->student_id] = $ByGroup;
            }

        }

            if (!empty($isDoneMark)){
                foreach ($isDoneMark as $st_id=>$stList){
                    foreach ($stList as $chekDateDone ){
                        if (!isset($CalendarGroupStatus[$chekDateDone][$st_id]['is_done'])){
                            $CalendarGroupStatus[$chekDateDone][$st_id]['is_done']=1;
                        }
                    }
                }
            }

        //
// 100% выполнено  #00B0CB
// в работе  #FC8440
// не начинал  #FF0000

        foreach ($CalendarGroup as   $studentList){
            foreach ($studentList as $ByGroup){
            $EventOne=[];

            $EventOne['start']=$ByGroup->date_start;
           // $EventOne['end']=$ByGroup->date_start;
            $EventOne['dateStart']=$ByGroup->date_start ;
             $EventOne['textColor'] ='#778192';
             $EventOne['allow_edit']=0;

            if ($group_id>0){
                $EventOne['group_id']=$group_id;
            }else{
                $EventOne['group_id']=$ByGroup->group_id;
            }

            if (isset($CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id])){
                if ($CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id]['is_done']==1) {
                    $EventOne['textColor'] ='#00B0CB';
                }
                if ($CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id]['is_done']==2) {
                    $EventOne['textColor'] ='#FC8440';
                }
                if ($CalendarGroupStatus[$ByGroup->date_start][$ByGroup->student_id]['is_done']==0) {
                    //$EventOne['textColor'] ='#FF0000';
                }
            }

            if (empty($ByGroup->group_id)){
                $EventOne['borderColor'] ='#63c2de';
                $EventOne['textColor'] ='#778192';
            }
            if ($ByGroup->student_id>0){
                $EventOne['allow_add']=0;
                $EventOne['allow_edit']=1;
                $lastName=mb_strtoupper(mb_substr($ByGroup->lastname,0,1)).'.';
                $EventOne['title']=$ByGroup->surname.' '.$lastName;
                $EventOne['url']='/admin/calendar/students/'.$ByGroup->student_id;
            }else{
                $EventOne['title']=' нет студентов';
            }

           $json_data[]=$EventOne;
        }
        }

        return response()->json($json_data);
    }

    public function GetEventStudent(Request $request){
        $start=$request->get('start');
        $end=$request->get('end');
        $student_id=$request->get('student_id',0);
        $student_id=(int)$student_id;
        if (empty($start)){
            $start='01'.date('-m-Y');
        }
        if (empty($end)){
            $end='31'.date('-m-Y');
        }
        $start_date=date('Y-m-d',strtotime($start));
        $dayEnd=date('Y-m-d',strtotime($end));
       $StudentObj= Student::where('id',$student_id)->first();
        $group_id= $StudentObj->group_id;
        //Выбираем учителей
        //->where('student.deleted',0)->where('student.blocked',0)
        $CalendarGroupObj=Student::where('student.id',$student_id)->where('student_training_task.is_self',0)
            ->leftjoin('student_training_task', 'student_training_task.student_id', '=', 'student.id')
            ->leftjoin('training_type', 'training_type.id', '=', 'student_training_task.training_type_id')
            ->select('student_training_task.id as task_id','student_training_task.date_start','student.id as student_id','student.surname','student.lastname','student_training_task.group_id','training_id','is_done','training_type.name as training_name','training_type.short_name as short_name','training_type.table_link as table_link' );

        $CalendarGroupObj->whereBetween('date_start',[$start_date,$dayEnd]);
        $CalendarGroup2=$CalendarGroupObj->distinct()->get();

          //  $sql = $CalendarGroupObj->toSql();
            //$bindings = $CalendarGroupObj->getBindings();
          //  dd($sql, $bindings);
//        if ($student_id==268){
//            print '<pre>';
//            $sql = $CalendarGroupObj->toSql();
//            $bindings = $CalendarGroupObj->getBindings();
//            dd($sql, $bindings);
//            die;
//        }

        $CalendarGroupOlimiada = OlympiadResult::where('olympiad_result.student_id', $student_id)
            ->leftjoin('student_training_task', function($join) {
                $join->on('student_training_task.student_id', '=', 'olympiad_result.student_id');
                $join->on('student_training_task.id', '=', 'olympiad_result.task_id');
                $join->on('student_training_task.training_id', '=', 'olympiad_result.training_id');
            })
            ->leftjoin('training_type', 'training_type.id', '=', 'student_training_task.training_type_id')
            ->select(
                'training_type.table_link',
                'training_type.name',
                'training_type.short_name',
                'student_training_task.id as task_id',
                'student_training_task.date_start',
                'student_training_task.student_id',
                'is_done',
                'is_result',
                'olympiad_result.task_id',
                'olympiad_result.time_memory',
                'olympiad_result.time_answer',
                'olympiad_result.date',
                'olympiad_result.total',
                'olympiad_result.good',
                'olympiad_result.bad',
                'olympiad_result.procent_ok'
            );
        $CalendarGroupOlimiada->where('is_done',1);
        $CalendarGroupOlimiada->where('student_training_task.is_self',0);
        $CalendarGroupOlimiada->whereBetween('student_training_task.date_start',[$start_date,$dayEnd]);
        $CalendarOlimiada=$CalendarGroupOlimiada->get();
        //$sql = $CalendarGroupOlimiada->toSql();
        //$bindings = $CalendarGroupOlimiada->getBindings();

        //dd($sql, $bindings);
//debug sql




        $CalendarOlimiadaList=[];
      foreach ($CalendarOlimiada as $OlimiadaResult){
                $CalendarOlimiadaList[$OlimiadaResult->date_start][$OlimiadaResult->task_id][]=(array)$OlimiadaResult->toArray();
            }


//      if ($student_id==1){
//          print '<pre>';
//            print_r($CalendarOlimiadaList);die;
//        }
        $json_data = [];
        $isDoneMark=[];
        //-- Пустой календарь, проверить есть ли ДЗ на дату
        //--- Auto-fix

        if (empty($CalendarGroup2) && !empty($student_id) && !empty($group_id)){
            $StudentGroupTask=StudentGroupTask::where('group_id',$group_id)->where('date_start','>=',date('Y-m-d'))->get();
            if (!empty($StudentGroupTask)){
                StudentGroupTask::AssignStudentTask($student_id, $group_id);
            }
        }

        foreach ($CalendarGroup2 as $GroupDate){
            $CalendarGroup[$GroupDate->date_start][]=$GroupDate;
            if ($GroupDate->is_done==1){
                $isDoneMark[]=$GroupDate->date_start;
            }elseif ($GroupDate->is_done==0){
                $CalendarGroupStatus[$GroupDate->date_start]['is_done']=0;
                //-- если олипиада взять ее данне

            }if ($GroupDate->is_done==2){
                $CalendarGroupStatus[$GroupDate->date_start]['is_done']=2;
            }
        }
        if (!empty($isDoneMark)){
            foreach ($isDoneMark as $chekDateDone){
                if (!isset($CalendarGroupStatus[$chekDateDone]['is_done'])){
                    $CalendarGroupStatus[$chekDateDone]['is_done']=1;
                }
            }
        }
// 100% выполнено  #00B0CB
// в работе  #FC8440
// не начинал  #FF0000
        $isShowDate=[];
        foreach ($CalendarGroup2 as $ByGroup){
            $EventOne=[];
            $EventOne['start']=$ByGroup->date_start;
            $EventOne['end']=$ByGroup->date_start;
            $EventOne['dateStart']=$ByGroup->date_start;
            $EventOne['textColor'] ='#778192';
            $EventOne['color'] ='#fff';
            $EventOne['day'] =date('j');
            $EventOne['is_fail'] =0;
            $EventOne['is_done'] =0;
            $EventOne['is_process'] =0;
            $EventOne['allow_add'] =1;
            $EventOne['sort_me'] =$ByGroup->task_id;

            if ($group_id>0){
                $EventOne['group_id']=$group_id;
            }else{
                $EventOne['group_id']=$ByGroup->group_id;
            }


            //-- Просрочка <10
            $now = new \DateTime(); // текущее время на сервере
            $date = \DateTime::createFromFormat("Y-m-d", $ByGroup->date_start); // задаем дату в любом формате
            $interval = $now->diff($date); // получаем разницу в виде объекта DateInterval

            $deltaDay = $interval->format('%R%a');
            $EventOne['title']='';
            if (in_array($ByGroup->date_start,$isShowDate)){
              //  continue;
            }

            if (empty($ByGroup->group_id)){
                $EventOne['borderColor'] ='#63c2de';
                $EventOne['color'] ='#fff';
            }

            $levelTask='-';
            $short_name='';
            $TrainingTaskParams=TraningHelper::TraningParams($ByGroup->task_id);//$ByGroup->training_id

            if (empty($ByGroup->short_name)){
                $short_name=$ByGroup->training_name;
            }else{
                $short_name=$ByGroup->short_name;
            }



            if (isset($TrainingTaskParams['TrainingTaskParams']['level'])){
                if ($TrainingTaskParams['TrainingTaskParams']['level']=='learn'){
                    $levelTask = __('admin.homework.form_elements.learn');
                }
                if ($TrainingTaskParams['TrainingTaskParams']['level']=='profi'){
                    $levelTask = __('admin.homework.form_elements.profi');
                }
                if ($TrainingTaskParams['TrainingTaskParams']['level']=='practice'){
                    $levelTask = __('admin.homework.form_elements.practice');
                }
            }
            $resultAdd='';
            if (isset($ByGroup['table_link'])){
                $chekOlimiad=substr($ByGroup['table_link'],0,8);

                if ($chekOlimiad=='olympiad'){
                    if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='practice'){
                        $levelTask = __('admin.homework.form_elements.easy');
                    }
                    if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='learn'){
                        $levelTask = __('admin.homework.form_elements.learn2');
                    }
                    if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='profi'){
                        $levelTask = __('admin.homework.form_elements.profi');
                    }

                    $task_id=$ByGroup->task_id;
                     $short_name=__('admin.homework.form_elements.olympiad_type').' '.$short_name;
                    if (!empty($CalendarOlimiadaList[$ByGroup->date_start][$task_id])){
                        $resultAdd='';
                        $numRepeat=1;
                        $totalRepeat=count($CalendarOlimiadaList[$ByGroup->date_start][$task_id]);
                        foreach ($CalendarOlimiadaList[$ByGroup->date_start][$task_id] as $resultStep){
                            $resultOlimiad=$resultStep;
                            $resultOlimiad['total']=$resultOlimiad['total']??0;
                            $resultOlimiad['good']=$resultOlimiad['good']??0;
                            //$procOK=0;
                            //if ($resultOlimiad['total']>0){
                            //    $procOK=round($resultOlimiad['good']/$resultOlimiad['total']*100);
                            //}
                            $TimeInfo='';// 'M:'.floor($resultOlimiad['time_memory']/60).'min A:'.floor($resultOlimiad['time_answer']/60).'min ';
                            $numRepeatText= ($totalRepeat>1) ?  '['.$numRepeat.']':'';

                            $minutesMemory = floor($resultOlimiad['time_memory'] / 60);
                            $secondsMemory = $resultOlimiad['time_memory'] % 60;
                            $minutesShow=($minutesMemory>=1) ? $minutesMemory.'m ': '';
                            $secondsShow=($secondsMemory>=1) ? $secondsMemory.'s': '';

                            $minutesAnswear = floor($resultOlimiad['time_answer'] / 60);
                            $secondsAnswear= $resultOlimiad['time_answer'] % 60;
                            $minutesAnswearShow=($minutesAnswear>=1) ? $minutesAnswear.'m ': '';
                            $secondsAnswearShow=($secondsAnswear>=1) ? $secondsAnswear.'s': '';

                            $addAnswer='/'.$minutesAnswearShow.$secondsAnswearShow;
                            $resultAdd.=$numRepeatText.' | А('.$resultOlimiad['good'].') | '.$minutesShow.$secondsShow.$addAnswer.' | Ball('.$resultOlimiad['total'].') |  '.date('d.m.Y H:i',strtotime($resultOlimiad['date'])) . "\r\n";

                            $EventOne['color'] ='#fff';
                            $EventOne['textColor'] ='#00B0CB';
                            $numRepeat++;
                        }



                    }
                }

            }

            if (isset($ByGroup['table_link']) && ($ByGroup['table_link']=='training_maths')) {

                if (isset($TrainingTaskParams['TrainingTaskParams']['category_maths'])){
                    if (isset($TrainingTaskParams['TrainingTaskParams']['category_maths'])){

                        $categoryName=\App\Helpers\SiteHelper::getCategoryMaths($TrainingTaskParams['TrainingTaskParams']['category_maths'])['label'];
                        $addComa= !empty($TrainingTaskParams['TrainingTaskParams']['comma_number']) ? '.('.$TrainingTaskParams['TrainingTaskParams']['comma_number'].')': '';

                        $addInfo="[{$TrainingTaskParams['TrainingTaskParams']['capacity']}$addComa]" ;
                        $ByGroup->training_name =$short_name= $ByGroup->training_name . ' : ' . $categoryName.$addInfo;
                    }
                }
            }
            if ($deltaDay <-10 ) {   //-- сейчас до 10 дней просрочил
                if ($ByGroup->is_done==1){
                    $statusShow=trans('student.hometask_info.succeeded');
                    $EventOne['color'] = '#00B0CB';
                    $EventOne['textColor'] ='white';
                }else{
                    $statusShow=trans('student.hometask_info.not_done');
                    $EventOne['color'] = 'pink';
                }


                if (stripos($resultAdd, 'ball') !== false) {
                    $statusShow='';
                }
                if ($CalendarGroupStatus[$ByGroup->date_start]['is_done'] == 0) {
                    $EventOne['is_fail'] = 1;
                    $EventOne['is_done'] = 0;
                    $EventOne['title'] =  $short_name.' ('.$levelTask.'): '.$statusShow;
                    $EventOne['desc'] =$ByGroup->training_name .' ('.$levelTask.')'  ;
                } else {
                    $EventOne['is_done'] = 1;
                    $EventOne['title'] =  $short_name.' ('.$levelTask.'): '. "\n". $resultAdd. $statusShow;

                    $EventOne['desc'] =$ByGroup->training_name .' ('.$levelTask.')'  ;
                }

                $isShowDate[]=$ByGroup->date_start;
                $json_data[] = $EventOne;
                continue;
            }


            if ($CalendarGroupStatus[$ByGroup->date_start]['is_done'] == 1) {
                $EventOne['is_done'] = 1;

                $Molodec=__('student.hometask_info.succeeded');
                if (stripos($resultAdd, 'ball') !== false) {
                    $Molodec='';
                }
                $EventOne['title'] = $short_name.' ('.$levelTask.'): '.$resultAdd. $Molodec;//'Молодець'
                $EventOne['color'] = '#00B0CB';

                $EventOne['desc'] =$ByGroup->training_name .' ('.$levelTask.')';

                $EventOne['textColor'] ='white';
                $isShowDate[]=$ByGroup->date_start;
                $json_data[] = $EventOne;
                continue;
            }


            if ($deltaDay<-10  &&  $CalendarGroupStatus[$ByGroup->date_start]['is_done']==0) { //Если просрочка
                $EventOne['is_start'] =0;
            }
           if($deltaDay>=0 ){ //будущее
                $EventOne['is_start'] =1;
                $EventOne['title'] = '';
            }else{ //от 0-10 дней
                $EventOne['title'] = '';
                if ($ByGroup->is_done==2){ //Start
                    $EventOne['is_start'] =1;
                }
            }
            if ($ByGroup->is_done==1) {
                $EventOne['textColor'] ='#00B0CB';
                $EventOne['color'] ='#fff';
            }
            if ($ByGroup->is_done==2) {
                $EventOne['textColor'] ='#FC8440';
            }
//            if ($ByGroup->is_done==0) {
//                $EventOne['textColor'] ='#FF0000';
//            }
            if ($ByGroup->is_done==1){
                $Molodec=__('student.hometask_info.succeeded');
                if (stripos($resultAdd, 'ball') !== false) {
                    $Molodec='';
                }
                $EventOne['title'] = $EventOne['title'] .' '.$resultAdd.' '.$Molodec;
            }
            $EventOne['title']= $short_name.' '.' ('.$levelTask.')'. ':'. $EventOne['title'];


           // $EventOne['desc'] =$ByGroup->training_name .' ('.$levelTask.')' ;
            $json_data[]=$EventOne;

        }

/*
        foreach ($CalendarOlimiada as $OlimiadaResult){
            $olimpiadResult=(array)$OlimiadaResult->toArray();
            $EventOne=[];

            $EventOne['start']=$olimpiadResult['date_start'];
            $EventOne['end']=$olimpiadResult['date_start'];
            $EventOne['dateStart']=$olimpiadResult['date_start'];
            $EventOne['textColor'] ='#778192';
            $EventOne['day'] =date('j');
            $EventOne['is_fail'] =0;
            $EventOne['is_done'] =0;
            $EventOne['is_process'] =0;
            $EventOne['allow_add'] =1;
            $TrainingTaskParams=SiteHelper::TraningParams($olimpiadResult['task_id']);//$ByGroup->training_id
            $levelTask='';

            if (isset($TrainingTaskParams['TrainingTaskParams']['evaluation'])){
                if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='profi'){
                    $levelTask = __('admin.homework.form_elements.profi');
                }
                if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='learn'){
                    $levelTask = __('admin.homework.form_elements.learn2');
                }
                if ($TrainingTaskParams['TrainingTaskParams']['evaluation']=='practice'){
                    $levelTask = __('admin.homework.form_elements.easy');
                }
            }
            if (!empty($olimpiadResult['is_result'])) {
                $EventOne['textColor'] ='white';
                $EventOne['color'] = '#00B0CB';

            }

            $nameOlimpiad=__('admin.homework.form_elements.olympiad_type').' '.$short_name;

            $procOK=0;
            if ($olimpiadResult['total']>0){
                $procOK=round($olimpiadResult['good']/$olimpiadResult['total']*100);
            }
            $EventOne['title'] = $nameOlimpiad.' ('.$levelTask.'): '.$olimpiadResult['good'].'/'.$olimpiadResult['total'] .' ('.$procOK.'%)';
            $EventOne['desc'] =__('admin.homework.form_elements.olympiad_type').' '.$olimpiadResult['name']. '('.$levelTask.')';

            $json_data[]=$EventOne;
        }
*/

        return response()->json($json_data);
    }

//    public function GetEvent(Request $request)
//    {
//     //  dd($request->all());
//        $json_data = [];
//        $EventOne['title']='Учитель 1';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;
//        if($request->franchiseeId ==1){
//        $EventOne['title']='Учитель 1';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==3){
//        $EventOne['title']='Учитель 2';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==4){
//        $EventOne['title']='Учитель 3';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==5){
//        $EventOne['title']='Учитель 4';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==6){
//        $EventOne['title']='Учитель 5';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==8){
//        $EventOne['title']='Учитель 6';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==10){
//        $EventOne['title']='Учитель 8';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//        if($request->franchiseeId ==11){
//        $EventOne['title']='Учитель 9';$EventOne['start']=date('Y-m-d');
//        $json_data[]=$EventOne;}
//
//       // dd($json_data);
//
////        $EventOne['title']='длинное событие';
////        $EventOne['start']=date('Y-m-d',strtotime("-4 days"));$EventOne['end']=date('Y-m-d',strtotime("4 days"));
//        $json_data[]=$EventOne;
//        $EventOne['title']='заметка';$EventOne['start']=date('Y-m-d',strtotime("+6 days"));$EventOne['notes']='Это нотес';
//        $json_data[]=$EventOne;
//        return response()->json($json_data);
//    }

    /**
     * Установить личное домашнее задание
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|RedirectResponse|Redirector|void
     */
    public function ResetTaskGroup(Request $request){
        $dates=$request->get('date_set');
        $student_id=$request->get('student_id');
        $student_id=(int)$student_id;

        $listParams['data']=date('d-m-Y',strtotime($dates));
        $listParams['student_id']=(int)$student_id;
        $listParams['is_private']=1;

        if ($student_id>0){
            $taskList=StudentTrainingTask::where('student_id',(int)$student_id)->where('is_self',0)->where('is_done',0)->where('date_start',date('Y-m-d',strtotime($dates)))->get();
            foreach ($taskList as $task){
                StudentTrainingTask::SetPrivate($task->id);
            }

            session()->put('homework', $listParams);
            if ($request->ajax()) {
                return ['redirect' => url('admin/homework'), 'message' => trans('admin.operation.succeeded')];
            }
            return redirect('admin/homework');
        }

    }

}
