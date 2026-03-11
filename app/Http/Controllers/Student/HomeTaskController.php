<?php
namespace App\Http\Controllers\Student;
use App\Helpers\TraningHelper;
use App\Http\Controllers\Controller;
use App\Models\StudentTrainingTask;
use App\Helpers\SiteHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class HomeTaskController extends Controller
{
    function index(Request $request){


        //Ищем первую кнопку старт

        $student_id = Auth::guard('student')->user()->id;

        $checkNotDone=StudentTrainingTask::where('student_id', $student_id)->where('is_self',0)->where('is_done',2)->first();
        if (!empty($checkNotDone) && $checkNotDone['id']>0){
            $taskMainId=session()->get('task_id');
            if (empty($taskMainId)){
                StudentTrainingTask::where('student_id', $student_id)->where('is_self',0)->where('is_done',2)->update(['is_done'=>0]);
             }else{
                $TrainingTaskParams= TraningHelper::TraningParams($checkNotDone['id']);

                return redirect('/student/'.$TrainingTaskParams['redirect'].'/'.$TrainingTaskParams['TrainingTaskParams']['level']);
            }

        }

        $end = '31' . date('-m-Y');
        $start_date = date('Y-m-d', strtotime("-10 days"));
        $dayEnd = date('Y-m-d', strtotime($end));

        $CalendarGroupObj = StudentTrainingTask::where('is_self', 0)->where('student_id', $student_id)->whereBetween('date_start', [$start_date, $dayEnd])->where('is_done',0)->orderBy('date_start','asc')->first();
        $currentDate=date('Y-m-d');
        if (!empty($CalendarGroupObj->id) && !empty($CalendarGroupObj->date_start)){
            $currentDate=$CalendarGroupObj->date_start;
        }
        session()->forget('interval');
        session()->forget('interval');

        return view('student.hometask.calendar', ['student_id' => $student_id,'currentDate'=>$currentDate]);
    }


    public function InfoTask(Request $request, $task_id_user = 0, $start = '') {

        session()->forget('training_words');
        session()->forget('interval');
        $task_id_user=(int)$task_id_user;


        $student_id = Auth::guard('student')->user()->id;
        $checkNotDone=StudentTrainingTask::where('student_id', $student_id)->where('is_self',0)->where('is_done',2)->first();

        if (!empty($checkNotDone) && $checkNotDone['id']>0){
            //Закончить все Домашние задания.
            StudentTrainingTask::where('student_id', $student_id)->where('is_self',0)->where('is_done',2)->update(['is_done'=>0]);
           // return redirect('/student/hometask-info/'.$checkNotDone['id']);
        }



        // Найти is_done=2
        $TrainingTask=StudentTrainingTask::where('student_training_task.id', $task_id_user)->where('is_self',0)
            ->leftjoin('training_type', 'training_type.id', '=', 'training_type_id')->select('student_training_task.*','table_link','name')
            ->where('student_id',$student_id)->orderBy('id','desc')->first();



        if (empty($TrainingTask['training_id'])){
            \Log::error('Error find Task for student_id='.$student_id.' student_training_task.id='.$task_id_user);

            return redirect('/student/hometask');
        }

        $date_start=  $TrainingTask->date_start;

        $TrainingTaskParams= TraningHelper::TraningParams($TrainingTask['id'])['TrainingTaskParams'];
        if (empty($TrainingTaskParams)){
            \Log::error('Error find Task Params for student_id='.$student_id.' student_training_task.id='.$task_id_user);
            return redirect('/student/hometask');

        }
        $TrainingTaskCnt=StudentTrainingTask::where('student_id',$student_id)->where('date_start',$date_start)->where('is_self',0)
            ->leftjoin('training_type', 'training_type.id', '=', 'training_type_id')->select('student_training_task.id as id','table_link','name','is_done','cnt_repeat')->orderBy('id','asc')
            ->get();

        $nextTaskId=0;
        $totalTask=count($TrainingTaskCnt);
          foreach ($TrainingTaskCnt as &$TrainingTaskList){
              $TrainingTaskList->TrainingTaskParams= TraningHelper::TraningParams($TrainingTaskList->id)['TrainingTaskParams'];
              if ($TrainingTaskList['is_done']==0 && empty($nextTaskId)){
                  $nextTaskId=$TrainingTaskList['id'];
                  $TrainingTaskParams=$TrainingTaskList->TrainingTaskParams;
              }
          }

        $TaskRun=0;

        foreach($TrainingTaskCnt as $TaskList){
            if (empty($TaskList->is_done)){
                if ($TaskList['TrainingTaskParams']!=null){

                    if (isset($TaskList['TrainingTaskParams']['repeat_number']) && isset($student_id) && $student_id>0 && $TaskList['cnt_repeat']>0 && $TaskList['cnt_repeat']>=$TaskList['TrainingTaskParams']['repeat_number']){
//                        if ($student_id==254){
//                            print 'PROBLEM HERE';
//                            print '<br>cnt_repeat='.$TaskList['cnt_repeat'];
//
//                            print '<br>repeat_number='.$TaskList['TrainingTaskParams']['repeat_number'];
//                        }
                        if ($TaskList['cnt_repeat']==$TaskList['TrainingTaskParams']['repeat_number']){
                            $setRepeat=(int)$TaskList['cnt_repeat']-1;
                            if ($setRepeat<0){
                                $setRepeat=0;
                            }
                            StudentTrainingTask::where('id', $TaskList->id)->update(array('cnt_repeat' =>$setRepeat));
                        }

                         //StudentTrainingTask::where('id', $TaskList->id)->update(array('is_done' => '1'));
                    }
                    $TaskRun+= (int)$TaskList['TrainingTaskParams']['repeat_number']-(int)$TaskList['cnt_repeat'];
                }
            }
        }

//        if ($student_id==254){
//            print '<pre>';
//            print '$TaskRun='.$TaskRun;
//            print '$nextTaskId='.$nextTaskId;
//
//            die;
//        }
        if (empty($nextTaskId)){
            return redirect('/student/hometask');
        }

        if (empty($TaskRun)){
            return redirect('/student/hometask');
        }

        $tpl='student.hometask.info';
          if (!empty($start)){
              $tpl='student.hometask.info_start';
          }
        $lang_locale = config('app.locale','pl');
        $default_params['languages_list']= SiteHelper::getLanguagesArray($lang_locale);
        $RetVars['totalTask']=$totalTask;
        $RetVars['date_start']=$date_start;
        $RetVars['TrainingTaskMain']=$TrainingTask;
        $RetVars['TrainingTaskList']=$TrainingTaskCnt;
        $RetVars['task_id']=$nextTaskId;
        $RetVars['ParamsTask']=$TrainingTaskParams;
        $RetVars['default_params']=collect($default_params ??[]);


        return view($tpl,$RetVars );
    }

    public function StartTask($id,Request $request) {
        $id=(int)$id;

        $student_id = Auth::guard('student')->user()->id;
        $TrainingTask=StudentTrainingTask::where('id', $id)->where('student_id',$student_id)->where('is_self',0)->first();
        if ($TrainingTask['is_done']!=0 && !empty($TrainingTask['date_start'])){
            $date_start=  $TrainingTask['date_start'];
            $TrainingTask=StudentTrainingTask::where('student_id',$student_id)->where('date_start',$date_start)->where('is_self',0)->where('is_done',0)->orwhere('is_done',2)->orderBy('id','asc')->first();
        }

        session()->put('resultSave',[]);
        session()->forget('level');
        session()->forget('ShufleTask');
        session()->forget('TrainingParams');
        session()->forget('TrainingTask');
        session()->forget('step');
        session()->forget('showHelp');
        session()->forget('interval');
        session()->forget('training_words');

        if (empty($TrainingTask['training_id'])){
            return redirect('/student/hometask');
        }

        StudentTrainingTask::SetProcess($id);
        $interval=$request->get('interval');
        $interval=(int)$interval;




        //----- Count Task
        $TraningParamsObj=TraningHelper::TraningParams($id);

        //-----------begin  Сменить язык для слов

            $table = $TraningParamsObj['TrainingTaskParams']->GetTable();

        if (($table == 'training_words' || $table == 'olympiad_words') && isset($request->languages_list)) {
            $languages_allow = array_column(SiteHelper::getLanguagesArray(), 'value');

            if (in_array($request->languages_list, $languages_allow)) {
                $TraningParamsObj['TrainingTaskParams']['lang'] = $request->languages_list;

                session()->put('training_words', ['lang' => $request->languages_list]);
            }
        }


        //----------- /End  Сменить язык для слов

        if (isset($interval) && $interval>0 && empty($TraningParamsObj['is_olympiad']) ){
            $min=1; $max=60;
            if (!empty($TraningParamsObj['TrainingTaskParams'])) {
                $table = $TraningParamsObj['TrainingTaskParams']->GetTable();
            }

            if ($table=='training_faces'){
                $min=1; $max=60; //sec
            }
            if ($table=='training_cards'){
                $min=1; $max=60;
            }

            if ($interval<=$min){
                $interval=$min;
            }
            if ($interval>=$max){
                $interval=$max;
            }

            $TraningParamsObj['TrainingTaskParams']['interval']=$interval*10;
            session()->put('interval',$interval*10);

        }

        $TrainingTaskParams=$TraningParamsObj['TrainingTaskParams'];
        $redirectModule= $TraningParamsObj['redirect'];


     if (!empty($TrainingTaskParams)){
         session()->put('level',$TrainingTaskParams['level']);
         session()->put('is_self',$TrainingTask['is_self']);
         session()->put('TrainingParams',$TrainingTaskParams);
         session()->put('StoreTraining',['TrainingParams'=>$TrainingTaskParams,'task_id'=>$id]);

     }else{
         die('Error Params Task');
     }

        return redirect('/student/'.$redirectModule.'/'.$TrainingTaskParams['level']);
    }

    public function GetEventStudent(Request $request){
        $start = $request->get('start');
        $end = $request->get('end');
        $student_id = Auth::guard('student')->user()->id;

        if (empty($start)) {
            $start = '01' . date('-m-Y');
        }
        if (empty($end)) {
            $end = '31' . date('-m-Y');
        }
        $start_date = date('Y-m-d', strtotime($start));
        $dayEnd = date('Y-m-d', strtotime($end));

        $CalendarGroupObj = StudentTrainingTask::where('is_self', 0);
        $CalendarGroupObj->whereBetween('date_start', [$start_date, $dayEnd]);
        $CalendarGroupObj->where('student_id', $student_id);
        $CalendarGroup2 = $CalendarGroupObj->orderBy('date_start','asc')->orderBy('group_id','asc')->distinct()->get();


        $end2 = '31' . date('-m-Y');
        $start_date2 = date('Y-m-d', strtotime("-10 days"));
        $dayEnd2 = date('Y-m-d', strtotime($end2));
        $CalendarIsStartFind = StudentTrainingTask::where('is_self', 0)->where('student_id', $student_id)->whereBetween('date_start', [$start_date2, $dayEnd2])->where('is_done',0)->orderBy('date_start','asc')->first();
        $dateStartBtn='';
        if (!empty($CalendarIsStartFind) &&  !empty($CalendarIsStartFind['date_start'])){
            $dateStartBtn=$CalendarIsStartFind['date_start'];
        }

        $json_data = [];
        $CalendarGroup=[];
        $CalendarGroupStatus=[];
        $isDoneMark=[];

    foreach ($CalendarGroup2 as $GroupDate){
        $CalendarGroup[$GroupDate->date_start][]=$GroupDate;
        if ($GroupDate->is_done==1 ){
            $isDoneMark[]=$GroupDate->date_start;
        }elseif ($GroupDate->is_done==0){
            if ($GroupDate->cnt_repeat>0){
                $CalendarGroupStatus[$GroupDate->date_start]['is_process']=1;
            }else{
                if (in_array($GroupDate->date_start,$isDoneMark)){
                    $CalendarGroupStatus[$GroupDate->date_start]['is_process']=1;
                }

            }
            $CalendarGroupStatus[$GroupDate->date_start]['is_done']=0;
        }elseif ($GroupDate->is_done==2){
            $CalendarGroupStatus[$GroupDate->date_start]['is_done']=2;
            $CalendarGroupStatus[$GroupDate->date_start]['is_process']=1;
        }
    }
    if (!empty($isDoneMark)){
        foreach ($isDoneMark as $chekDateDone){
            if (!isset($CalendarGroupStatus[$chekDateDone]['is_done'])){
                $CalendarGroupStatus[$chekDateDone]['is_done']=1;
            }
        }
    }


    //print '<pre>';
//print_r($CalendarGroupStatus);die;
// 100% выполнено  #00B0CB
// в работе  #FC8440
// не начинал  #FF0000
// Звездочка  и убрать текст.

        $ButtonStart=0;
        $maxDay=1; //+1 день максимум до выполнения

        $isShowDate=[];
        $cntTreningDay=[];
            foreach ($CalendarGroup2 as $ByGroup) {
                $EventOne = [];
                $EventOne['start'] = $EventOne['end'] = $EventOne['dateStart'] = $ByGroup->date_start;
                $EventOne['day'] = date('j', strtotime($ByGroup->date_start));

                $EventOne['is_fail'] = 0;
                $EventOne['is_done'] = 0;
                $EventOne['is_process'] = 0;
                $EventOne['BorderColor'] = 'none';
                $EventOne['test'] =0;
                //-- Просрочка <10
                $now = new \DateTime(); // текущее время на сервере
                $date = \DateTime::createFromFormat("Y-m-d", $ByGroup->date_start); // задаем дату в любом формате
                $difDate = $now->diff($date); // получаем разницу в виде объекта DateInterval

                $deltaDay = $difDate->format('%R%a');
              //  $deltaDay = $interval->d;
                $EventOne['title'] = ' ';
                $EventOne['deltaDay'] = $deltaDay;
                if (in_array($ByGroup->date_start,$isShowDate)){
                    continue;
                }


                if ($deltaDay <-10 ) {   //-- сейчас до 10 дней просрочил
                    if (isset($CalendarGroupStatus[$ByGroup->date_start]) && $CalendarGroupStatus[$ByGroup->date_start]['is_done'] == 0) {
                        $EventOne['is_fail'] = 1;
                        $EventOne['title'] = trans('student.hometask_info.not_done');
                        $EventOne['color'] = 'white';
                        $EventOne['textColor'] = '#000';
                        $EventOne['borderColor'] ='red';
                    } else {
                        $EventOne['is_done'] = 1;
                        $EventOne['title'] =trans('student.hometask_info.succeeded'). ' :)';
                       //$EventOne['color'] = '#00B0CB';
                        $EventOne['backgroundColor'] = 'white';
                        $EventOne['textColor'] = '#000';
                        $EventOne['Color'] = 'white';
                        $EventOne['borderColor'] ='orange';
                    }
                    $isShowDate[]=$ByGroup->date_start;
                    $json_data[] = $EventOne;
                    continue;
                } else {
                    if (isset($CalendarGroupStatus[$ByGroup->date_start]) && $CalendarGroupStatus[$ByGroup->date_start]['is_done']==1){
                        $EventOne['is_done'] = 1;
                        $EventOne['title'] = trans('student.hometask_info.succeeded'). ' :)';
                        //$EventOne['color'] = '#00B0CB';
                        $EventOne['backgroundColor'] = 'white';
                        $EventOne['textColor'] = '#000';
                        $EventOne['Color'] = 'white';
                        $EventOne['borderColor'] ='orange';
                        $json_data[] = $EventOne;
                        $isShowDate[]=$ByGroup->date_start;
                        continue;
                    }
                }

                if (!empty($CalendarGroupStatus[$ByGroup->date_start]['is_process']) or (!empty($isDoneMark) && in_array($ByGroup->date_start,$isDoneMark)) ){//
                    $EventOne['is_process'] = 1;
                    $EventOne['color'] = '#f3e5ab';
                    $EventOne['test'] =1;
                }

                if (($deltaDay>=-10 && $deltaDay <= $maxDay) && $ButtonStart == 0 &&  ( (!empty($dateStartBtn) && $dateStartBtn==$ByGroup->date_start) || (empty($ByGroup->date_start) && $CalendarGroupStatus[$ByGroup->date_start]['is_done']==0)))
                {
                    $ButtonStart = 1;

                    //$EventOne['is_process'] = 0;
                    $EventOne['backgroundColor'] = 'white';
                    $EventOne['textColor'] = 'white';
                    $EventOne['Color'] = 'white';
                    $EventOne['borderColor'] ='white';

                    $NextTaskObj= StudentTrainingTask::where('student_id',$student_id)->where('is_self',0)->where('is_done',0)->where('date_start',$ByGroup->date_start)->orderBy('id','asc')->first();
                    if (!empty($NextTaskObj)){
                        $EventOne['is_start'] = 1;
                        $EventOne['url_start'] = '/student/hometask-info/' . $NextTaskObj['id'];
                        $EventOne['title_name'] = trans('student.hometask_info.training');
                        $isShowDate[]=$ByGroup->date_start;
                    }
                }else{
                    if (empty($cntTreningDay[$ByGroup->date_start])){
                        $cntTreningDay[$ByGroup->date_start]=1;
                        $EventOne['title_name'] = trans('student.hometask_info.training');
                        $EventOne['is_start'] = 2;
                        $EventOne['url_start'] = '/student/hometask-info/' . $ByGroup->id;


                        $EventOne['backgroundColor'] = 'white';
                        $EventOne['textColor'] = '#000';
                        $EventOne['Color'] = 'white';
                        $EventOne['borderColor'] ='#ddd';


                        $indexData=count($json_data);
                    }else{
                        continue;

                    }

                }

                $json_data[] = $EventOne;
            }


        return response()->json($json_data);
    }
}//end HomeTaskControler
