<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Helpers\TraningHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeWork\DestroyHomework;
use App\Http\Requests\Admin\HomeWork\IndexHomework;
use App\Http\Requests\Admin\HomeWork\StoreHomework;
use App\Http\Requests\Admin\HomeWork\UpdateHomework;


use App\Models\Franchisee;

use App\Models\OlympiadBinnary;
use App\Models\OlympiadCards;
use App\Models\OlympiadFaces;
use App\Models\OlympiadHistory;
use App\Models\OlympiadMemory;
use App\Models\OlympiadNumberLetter;
use App\Models\OlympiadWords;
use App\Models\Student;
use App\Models\StudentGroupTask;
use App\Models\Teacher;
use App\Models\TeacherGroup;
//--------- params begin
use App\Models\TrainingBinnary;
use App\Models\TrainingFaces;
use App\Models\TrainingMaths;
use App\Models\TrainingNumberLetter;
use App\Models\TrainingWords;
use App\Models\TrainingAssociative;
use App\Models\TrainingTransform;
use App\Models\TrainingMemory;
use App\Models\TrainingCards;
use App\Models\TrainingAbacus;
use App\Models\TrainingHistory;
//--------- params end
use App\Models\TrainingType;

use App\Models\StudentTrainingTask;


use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class HomeworkController extends Controller
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
    public function redirectCreate($dates='',$fields ='',  $params=0,$action='create'){
        $listParams['data']=$dates;
        $listParams[$fields]=$params;
        if ($fields=='student_id'){
            $listParams['is_private']=1;

            $listParams['group_id']=Student::where('id',(int)$params)->first()->group_id;
            $listParams['student_id']=(int)$params;
        }else{
            $listParams['is_private']=0;
        }


        session()->put('homework', $listParams);
        if ($action=='create'){
            return redirect('/admin/homework/create');
        }else{
            return redirect('/admin/homework');
        }
    }
    public function redirectPrivate($dates='',$fields ='',  $params=0,$action='create'){
        $listParams['data']=$dates;
        $listParams[$fields]=$params;
        $listParams['is_private']=1;

        session()->put('homework', $listParams);
        if ($action=='create'){
            return redirect('/admin/homework/create');
        }else{
            return redirect('/admin/homework');
        }
    }
    public function redirectList($dates='',$fields ='',  $params=0){

        return $this->redirectCreate($dates,$fields,  $params,'list');

    }
    /**
     * Display a listing of the resource.
     *
     * @param IndexFranchisee $request
     * @return array|Factory|View
     */
    public function index(IndexHomework $request,$student_id=0)
    {

        $listParams=session()->get('homework');


        if (empty($listParams)){
            return redirect('/admin/calendar');
        }
        $studentBreadCrumb=[];
        if (!empty($listParams['is_private'])){

            $studentBreadCrumb=Student::where('id',$listParams['student_id'])->first();
            (int)$listParams['group_id']=$studentBreadCrumb['group_id'];
        }
            $groupBreadCrumb =TeacherGroup::where('id',(int)$listParams['group_id'])->first();
            $teacherBreadCrumb =Teacher::where('id',(int)$groupBreadCrumb->teacher_id)->first();
            $franchiseeBreadCrumb =Franchisee::where('id',(int)$groupBreadCrumb->franchisee_id)->first();




        $dataFilter=date('Y-m-d',strtotime($listParams['data']));


        if (empty($listParams['is_private'])){
            $listParams['allow_clone']=1;
        $dataObj=StudentGroupTask::where('student_group_task.enabled',1)
            ->leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
         ->where('student_group_task.enabled', 1)

         ->where('date_start',$dataFilter)
         ->where('group_id',$listParams['group_id'])
         ->select('student_group_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link','student_group_task.created_at','student_group_task.id as group_id');
        }else{
            $dataObj=StudentTrainingTask::leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
                ->where('date_start',$dataFilter)
                ->where('is_self', 0)

                ->where('student_id',(int)$listParams['student_id'])
                ->select('student_training_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link','student_training_task.created_at','student_training_task.group_id');
        }
        $data=$dataObj->get();
        $dataNew=$data;
        $listParams['allow_clone']=1;
        if (!empty($data)) {
            foreach ($data as $k=>$items) {
                $tableName=$items->table_link;

                if ($items['group_id']>0 && isset($listParams['is_private']) && $listParams['is_private']==1){
                    //если хоть 1 груповое запрет на копирование
                    $listParams['allow_clone']=0;
                }
                if (!empty($tableName)){
                    $items->properties=(array)DB::table($tableName)->where("id", $items->training_id)->first();
                    if (isset($items->properties['range_value'])){
                        if (isset($items->properties['range_type']) && $items->properties['range_type']=='even'){
                            $range_valueArray =json_decode( $items->properties['range_value']);
                        }else{
                            $range_valueArray =  $items->properties['range_value'] ;
                        }
                    }

                   if(!empty($range_valueArray) && is_array($range_valueArray)) {
                       $items->range_valueArray = $range_valueArray;
                   }
                }else{
                    $items->properties=[];
                }
                $dataNew[$k]=$items;
            }
        }

        return view('admin.homework.index',  ['data' => $dataNew,'listParams'=>$listParams,'franchiseeBreadCrumb'=>$franchiseeBreadCrumb, 'teacherBreadCrumb'=>$teacherBreadCrumb,'groupBreadCrumb'=>$groupBreadCrumb,'studentBreadCrumb'=>$studentBreadCrumb  ]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create($student_id=0){
        $studentBreadCrumb=[];
        $student_id=(int)$student_id;
        $listParams=session()->get('homework');
        if (empty($listParams)){
            return redirect('/admin/calendar');
        }

        $trainingTypes =  TrainingType::where('enabled',1)->orderBy('olympiad')->get();

        foreach ($trainingTypes as $trainingList){
            if ($trainingList->olympiad==1){
                $trainingList->name= __('admin.forms.Olimiad').' "'.  $trainingList->name.'"';
            }
        }
        $ViewParams['capacity'] = SiteHelper::getCapacityArray();
        $ViewParams['capacity_olypiad']=SiteHelper::getCapacityOlimpiadArray();
        $ViewParams['digit_number']= SiteHelper::getDigitNumberArray();
        $ViewParams['digitIntervals']=SiteHelper::getDigitIntervalsArray();
        $ViewParams['digitIncrements']= SiteHelper::getDigitIncrementsArray();
        $ViewParams['intervals']= SiteHelper::getIntervalsArray();
        $ViewParams['intervals_memory']=SiteHelper::getIntervalsOlimpiadMemory();
        $ViewParams['repeat_number']=  SiteHelper::getRepeatNumberArray();
        $ViewParams['intervals_olimpiad'] = SiteHelper::getIntervalsOlimpiadShow();


        $ViewParams['сategory_list'] = SiteHelper::getCategoryArray();

//-------- Maths begin
        $ViewParams['сategoryMaths'] = SiteHelper::getCategoryMaths();
        $ViewParams['capacity_maths_list'] = SiteHelper::getCapacityMathsArray();

        $ViewParams['capacity_maths_list2'] = SiteHelper::getCapacity2MathsArray();
        $ViewParams['div_capacity_list'] = SiteHelper::getDivCapacity();
        $ViewParams['div_action'] = SiteHelper::getDivActionArray();
        $ViewParams['procent_level_list'] = SiteHelper::getProcentsLevelArray();
        $ViewParams['cnt_operation_list'] = SiteHelper::getCntOperaion();
        $ViewParams['comma_number_list'] = SiteHelper::getCommaNumber();
        $ViewParams['div_action_fraction_list'] = SiteHelper::getDivActionFraction();
        //-------- Maths end

        //-------- faces begin
        $ViewParams['category_faces_list'] = SiteHelper::getCategoryFacesArray();
        $ViewParams['capability_faces_list'] = SiteHelper::getCapabilityFacesArray();
        $ViewParams['gender_list'] = SiteHelper::getGenderFacesArray();
        //-------- faces begin
        //-------- cards begin
        $ViewParams['group_cards_list'] = SiteHelper::getCardGroups();
        $ViewParams['pack_cards_list'] = SiteHelper::getCardPack();
        //-------- cards end
        if (!empty($listParams['is_private'])){
            $studentBreadCrumb=Student::where('id',$listParams['student_id'])->first();
            (int)$listParams['group_id']=$studentBreadCrumb['group_id'];
        }
        //-------- history begin
        $ViewParams['category_history_list'] = SiteHelper::getCategoryHistory();
        //$ViewParams['capability_faces_list'] = SiteHelper::getCapabilityFacesArray();
        //$ViewParams['gender_list'] = SiteHelper::getGenderFacesArray();
        //-------- history begin
        $groupBreadCrumb =TeacherGroup::where('id',(int)$listParams['group_id'])->first();
        $defaultParamsVals['interval_list']=SiteHelper::getIntervalsArray(10);
        $defaultParamsVals['сategory_list']=SiteHelper::getCategoryArray('noun-noun');
        $defaultParamsVals['training_type_id']=$trainingTypes[0];
        $defaultParamsVals['level']='learn';
        $defaultParamsVals['digit_number_list']='10';
        $defaultParamsVals['digit_number']='10';
        $defaultParamsVals['range_type']='even';
        $defaultParamsVals['range_value']=0;
        $defaultParamsVals['evaluation'] ='practice';

//
//        if (!empty($listParams)){
//            $default_params=$listParams;
//            if (!empty($listParams['category'])){
//                $default_params['сategory_list']=SiteHelper::getCategoryArray($listParams['category']);
//            }
//            if (!empty($listParams['category_maths'])){
//                $default_params['category_maths_list']=SiteHelper::getCategoryMaths($listParams['category_maths']);
//            }
//            if (!empty($listParams['div_action'])){
//                $default_params['div_action_list']=SiteHelper::getDivActionArray($listParams['div_action']);
//            }
//            if (!empty($listParams['procent_level'])){
//                $default_params['procent_level_list']=SiteHelper::getProcentsLevelArray($listParams['procent_level']);
//            }
//
//            if (!empty($listParams['category_faces'])){
//                $default_params['category_faces_list']=SiteHelper::getCategoryFacesArray($listParams['category_faces']);
//            }
//
//            if (!empty($listParams['category_history'])){
//                $default_params['category_history_list']=SiteHelper::getCategoryFacesArray($listParams['category_history']);
//            }
//            if (!empty($listParams['gender'])){
//                $default_params['gender_list']=SiteHelper::getGenderFacesArray($listParams['gender']);
//            }
//            if (isset($listParams['category_faces'])) {
//                if (empty($listParams['capability_faces'])) {
//                    $listParams['capability_faces']=0;
//                }
//                $default_params['capability_faces_list'] = SiteHelper::getCapabilityFacesArray($listParams['capability_faces']);
//            }
//        }

        //------ Cards Default
        if (empty($defaultParamsVals['suits'])){
            $defaultParamsVals['suits_s']=1;
            $defaultParamsVals['suits_h']=1;
            $defaultParamsVals['suits_c']=1;
            $defaultParamsVals['suits_d']=1;
        }
        $det_suits=['S','H','C','D'];
        foreach ($det_suits as $suit){
            $defaultParamsVals['suits_'.$suit]=0;
            if (isset($defaultParamsVals['suits']) && strpos($defaultParamsVals['suits'],$suit)!==false){
                $defaultParamsVals['suits_'.$suit]=1;
            }
        }

        $defaultParamsVals['div_suits']=$defaultParamsVals['div_suits']??1;
        $defaultParamsVals['group_cards']=$defaultParamsVals['group_cards']??1;
        $defaultParamsVals['group_cards_list'] = SiteHelper::getCardGroups($defaultParamsVals['group_cards']);
        $defaultParamsVals['pack_cards']=$defaultParamsVals['pack_cards']??1;
        $defaultParamsVals['pack_cards_list'] = SiteHelper::getCardPack($defaultParamsVals['pack_cards']);
        //------ Cards Default --/end

        $defaultParamsVals['category_binary_list']=SiteHelper::getBinnaryCatergory($defaultParamsVals['category_id']??1);
        $defaultParamsVals['categoryBinaryFlag']=$defaultParamsVals['category_id']??1;

        if (empty($listParams['div_action'])){
            $defaultParamsVals['div_action']= 'all';
        }
        if (empty($listParams['category_maths_list'])){
            $defaultParamsVals['category_maths_list']= SiteHelper::getCategoryMaths('random');
        }


        $defaultParamsVals['capacity_maths_list2']= SiteHelper::getCapacity2MathsArray(1);
        $defaultParamsVals['procent_level_list']=SiteHelper::getProcentsLevelArray('all');

        if (!empty($listParams['comma_number'])){
            $defaultParamsVals['comma_number_list']=SiteHelper::getCommaNumber('all');
        }

        if (empty($listParams['cnt_operation_list'])){
            $defaultParamsVals['cnt_operation_list']= SiteHelper::getCntOperaion(1);
        }
        if (empty($listParams['div_comma'])){
            $defaultParamsVals['div_comma']= 0;
        }
        if (empty($listParams['cnt_operation'])){
            $defaultParamsVals['cnt_operation']= 0;
        }
        $defaultParamsVals['repeat_number_list']=SiteHelper::getRepeatNumberArray(1);

        $defaultParams = collect($defaultParamsVals);


        $ViewParams['defaultParams']=$defaultParams;
        $ViewParams['listParams']=$listParams;
        $ViewParams['trainingTypes']=$trainingTypes;
        $ViewParams['student_id']=$student_id;
        $ViewParams['groupBreadCrumb']=$groupBreadCrumb;
        $ViewParams['studentBreadCrumb']=$studentBreadCrumb;
        $ViewParams['onDate']=$listParams['data'];


        return view('admin.homework.create', $ViewParams );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFranchisee $request
     * @return array|RedirectResponse|Redirector
     */
    private function ParamsByTask($request,$sanitized,$tableLink) {
        //-------------- for ALL
        $sanitized['interval'] =(int) $request->getIntervals(); //для всех
        $sanitized['repeat_number'] =(int) $request->getRepeatCount();
        if (empty( $sanitized['repeat_number'])){
            $sanitized['repeat_number']=1;
        }

        $sanitized['interval_memory'] =(int) $request->getIntervalsMemory();

        if (isset($sanitized['digit_number_list'])){
            $sanitized['digit_number'] = $sanitized['digit_number_list'];
        }
        if (empty($sanitized['digit_number'])){
            $sanitized['digit_number']=1;
        }
        if (isset($sanitized['digit_number'])){
            $sanitized['digit_number']=(int)$sanitized['digit_number'];
        }
        //-------------- / end for ALL

        switch ($tableLink) {
            case  'training_number_letter': {
                    if ($sanitized['level'] != 'learn') {
                        $sanitized['capacity'] = $request->getCapacity();
                        $sanitized['digit_number'] = $sanitized['digit_number_list'];
                    } else {
                        $sanitized['capacity'] = 0;
                        $sanitized['digit_number'] = 0;
                    }
                    if ($sanitized['range_type'] == 'even') {
                        $sanitized['range_value'] = json_encode($request->getRangeEventValue());
                    } else {
                        $sanitized['range_value'] = $request->getRangeIncrementValue();
                    }
                    $sanitized['capacity'] = (int)($sanitized['capacity'] ??2);
                    if ($sanitized['level'] == 'learn') {
                        $sanitized['capacity'] = 2;
                        $sanitized['digit_number'] = 10;
                        if (isset($sanitized['range_type']) && $sanitized['range_type'] == 'even') {
                            $listRangeFull = $request->getRangeEventValue();

                            $digit_number = 0;
                            if (!empty($listRangeFull)) {
                                foreach ($listRangeFull as $Range) {
                                    $listRange = explode('-', $Range['value']);
                                    $digit_number += count(range($listRange[0], $listRange[1]));
                                }
                            }

                            if ($digit_number < 10) {
                                $digit_number = 10;
                            }
                            $sanitized['digit_number'] = $digit_number;

                        } else { //Довiльнi
                            $listRange = explode('-', $sanitized['range_value']);
                            $sanitized['digit_number'] = count(range($listRange[0], $listRange[1]));
                        }
                    }
                }

                break;
            case  'training_words':
                $sanitized['digit_number'] = $sanitized['digit_number_list'];
                $sanitized['category'] = $request->getCategory();
                if (empty($sanitized['category'])){
                    $sanitized['category'] = 'noun-noun';
                }

                break;
            case  'training_associative':
                $sanitized['repeat_number'] = $request->getRepeatCount();

                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                  }
                $sanitized['repeat_number']=(int)$sanitized['repeat_number'];
                break;
            case  'training_faces':
                unset( $sanitized['category_list']);
                unset( $sanitized['category']);
                unset( $sanitized['range_type']);
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=(int)($sanitized['repeat_number']??1);
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                if ($sanitized['level'] == 'learn') {
                    $sanitized['gender'] = $request->getGender();
                    $sanitized['capability_faces'] =0;
                }
                $sanitized['category_faces'] = $request->getCategoryFaces();
                //  $ViewParams['gender_list'] = SiteHelper::getGenderFacesArray();
                break;
            case  'training_cards':
                unset( $sanitized['category_list']);
                unset( $sanitized['category']);
                unset( $sanitized['range_value']);
                unset( $sanitized['range_type']);
                unset( $sanitized['capacity']);
                $sanitized['digit_number'] = $sanitized['digit_number_list'];
                if (empty($sanitized['digit_number'])){
                    $sanitized['digit_number']=1;
                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=(int)($sanitized['repeat_number']??1);
                $sanitized['suits'] = $request->getSuits();
                $sanitized['div_suits'] = $request->getDivSuits();
                $sanitized['group_cards'] = $request->getGroupCards();
                $sanitized['pack_cards'] = $request->getPackCards();
                $sanitized['interval'] = $request->getIntervals();

                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;
            case  'training_memory':
                if ($sanitized['level']=='learn'){
                    $sanitized['level']='practice';
                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;
            case  'training_abacus':
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=(int)($sanitized['repeat_number']??1);
                unset($sanitized['capacity'] );
                unset($sanitized['capacity'] );
                //unset($sanitized['category_list'] );
                unset($sanitized['capability_faces_list'] );
                unset($sanitized['category_faces_list'] );
                unset($sanitized['category_history_list'] );
                unset($sanitized['pack_cards_list'] );
                unset($sanitized['group_cards'] );
                unset($sanitized['div_suits'] );
                unset($sanitized['category'] );
                unset($sanitized['range_type'] );
                unset($sanitized['suits_s'],$sanitized['suits_h'],$sanitized['suits_c'],$sanitized['suits_d'] );


                $sanitized['interval'] = $request->getIntervals();

                $sanitized['category_abacus']=$request->input('category_abacus_list')['value']??'all';
                $sanitized['capacity_abacus']=$request->input('capacity_abacus_list')['value']??1;


                if  ($sanitized['digit_number']>25){
                    $sanitized['digit_number']=25;
                }

                break;
            case  'training_history':
                if ($sanitized['level']=='learn'){
                    $sanitized['level']='practice';
                }

                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=(int)($sanitized['repeat_number']??1);
                $sanitized['category_history'] = $request->getCategoryHistory();
                if ($sanitized['range_type'] == 'even') {
                    $sanitized['range_value'] = json_encode($request->get('range_even_history_list',[]));
                } else {
                    $sanitized['range_value'] = $request->getRangeIncrementValue();
                }
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;

            case  'training_binnary':
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=(int)($sanitized['repeat_number']??1);
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                unset($sanitized['capacity'] );
                if ($sanitized['level']=='learn'){
                    $sanitized['digit_number']=8;
                }
                if ($sanitized['level']=='practice'){
                    $sanitized['show_groups']=$request->input('show_groups_list')['value']??2;
                    $sanitized['category_id']=0;
                    if  ($sanitized['digit_number']>=100){
                        $sanitized['digit_number']=100;
                    }
                }
                if ($sanitized['level']=='profi'){
                    $sanitized['show_groups']=$request->input('show_groups_list')['value']??2;
                    $sanitized['category_id']=$request->input('category_binary')['value']??2;
                    if  ($sanitized['digit_number']>=1000){
                        $sanitized['digit_number']=1000;
                    }
                }
                break;

            case  'training_maths':
                unset( $sanitized['category_list']);
                unset( $sanitized['category']);
                unset( $sanitized['range_type']);
                if (!isset($sanitized['div_comma'])){
                    $sanitized['div_comma']=0;
                }
                $sanitized['category_maths'] = $request->getCategoryMaths();
                $sanitized['capacity'] = $request->getCapacityMaths();


                $sanitized['cnt_operation'] = $request->getCntOperaion();
                $sanitized['div_action'] = $request->getDivAction();
                $sanitized['procent_level'] ='all';

                if ($sanitized['category_maths']=='percentage') {
                    $sanitized['level'] = 'practice';
                    $sanitized['procent_level'] = $request->getProcentLevel();
                }
                if ($sanitized['category_maths']=='multiplication'){
                    $sanitized['comma_number'] = $request->getCapacity2Maths();
                }elseif ($sanitized['category_maths']=='random'){
                    $sanitized['level']='practice';
                    $sanitized['cnt_operation'] = 1; //Одна операция
                    $sanitized['procent_level'] = $request->getProcentLevel();
                    $sanitized['comma_number'] = $request->getCommaNubmer();
                    if (empty($sanitized['comma_number'] )) {
                        $sanitized['comma_number'] = 0;
                    }

                }elseif ($sanitized['category_maths']=='division'){
                    $sanitized['capacity'] = $request->getDivCapacity();
                    $sanitized['comma_number'] =  $sanitized['div_comma'];
                    $sanitized['cnt_operation'] =  1;
                    $sanitized['procent_level'] =  'all';
                } elseif ($sanitized['category_maths']=='fractions'){
                    $sanitized['level'] = 'practice';
                    $sanitized['comma_number'] = 2;
                    $sanitized['cnt_operation'] = 1; //Одна операция
                    $sanitized['div_action'] = $request->getFractionAction();

                }else{
                    $sanitized['comma_number'] =  $sanitized['div_comma'];
                }

                if (empty($sanitized['cnt_operation'])){
                    $sanitized['cnt_operation'] =1;
                }
                if ( $sanitized['level']!=='practice' && $sanitized['level']!=='profi'){
                    $sanitized['level']= 'practice';
                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;


            case  'olympiad_number_letter':
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['capacity'] = $request->getCapacity();
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';

                if ($sanitized['evaluation']=='practice'){
                    $sanitized['range_type'] ='increment';
                    $sanitized['range_value'] =$request->range_increment_olist['value']??'0-99';
                }else{
                    $sanitized['range_value'] =null;
                    $sanitized['range_type'] =null;
                }

                $sanitized['level'] ='profi';
                if (empty($sanitized['capacity'])){
                    $sanitized['capacity']=0;
                }

                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;
            case  'olympiad_words':
                $sanitized['capacity'] = $request->getCapacity();
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['capacity']= $sanitized['capacity']??0;
                $sanitized['repeat_number']= $sanitized['repeat_number']??1;
                $sanitized['level'] ='profi';

             break;
            case  'olympiad_memory':
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
            break;
            case  'olympiad_binnary':
                unset($sanitized['capacity'] );
                unset($sanitized['capacity_list']);
                $sanitized['category_id'] =$request->get('category_binary')['value']??1;
                $sanitized['show_groups'] =$request->get('show_groups_list')['value']??0;
                if   ($sanitized['category_id']==1 &&  $sanitized['show_groups']>2){
                    $sanitized['show_groups'] =2;
                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;
            case  'olympiad_cards':
                unset($sanitized['capacity'] );
                unset($sanitized['capacity_list']);
                unset($sanitized['category_binary']);
                $sanitized['category_id'] =$request->get('category_binary')['value']??1;
                $sanitized['show_groups'] =$request->get('show_groups_list')['value']??0;
                $sanitized['group_cards'] =$request->get('group_cards_list')['value']??1;
                $sanitized['pack_cards'] =$request->get('pack_cards_list')['value']??1;

                if   ($sanitized['category_id']==1 &&  $sanitized['show_groups']>2){
                    $sanitized['show_groups'] =2;
                }

                $sanitized['repeat_number'] = $request->getRepeatCount();

                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';
                if (empty( $sanitized['repeat_number'])) {
                    $sanitized['repeat_number'] = 1;
                }
                break;
            case  'olympiad_faces':
                unset($sanitized['capacity'] );
                unset($sanitized['capacity_list']);

                if ($sanitized['range_type'] == 'even') {
                    $sanitized['range_value'] = json_encode($request->getRangeEventValue());
                } else {
                    $sanitized['range_value'] = $request->getRangeIncrementValue();

                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number']=  $sanitized['repeat_number']??1;
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';

                break;
            case  'olympiad_history':
                unset($sanitized['capacity'] );
                unset($sanitized['capacity_list']);

                $sanitized['category_history']=2;
                $sanitized['range_type']='increment';
                $sanitized['range_value']='900-2099';

//                if ($sanitized['range_type'] == 'even') {
//                    $sanitized['range_value'] = json_encode($request->getRangeEventValue());
//                } else {
//                    $sanitized['range_value'] = $request->getRangeIncrementValue();
//                }
                $sanitized['repeat_number'] = $request->getRepeatCount();
                $sanitized['repeat_number'] =$sanitized['repeat_number'] ??1;
                $sanitized['interval'] = $request->getIntervalsOlympiad();
                $sanitized['level'] ='profi';

                break;
            default:
                break;

        }//end switch

//========== For All Limit
        if ($sanitized['level']=='practice'){
            if (empty($sanitized['digit_number']) && (int)$sanitized['digit_number']<1){
                $sanitized['digit_number']=1;
            }
            if ((int)$sanitized['digit_number']>300){
                $sanitized['digit_number']=300;
            }
        }
        if ($sanitized['level']=='profi'){
            if (empty($sanitized['digit_number']) && (int)$sanitized['digit_number']<1){
                $sanitized['digit_number']=1;
            }
            if ((int)$sanitized['digit_number']>1000){
                $sanitized['digit_number']=1000;
            }
        }
        if (empty($sanitized['interval'])){
            $sanitized['interval']=10;
        }

        return $sanitized;
    }
    public function store(StoreHomework $request) {

        $listParams=session()->get('homework');
        if (empty($listParams)){
            return redirect('/admin/calendar');
        }

        $sanitized = $request->getSanitized();

        $training_type_id =(int) $request->getTypeTraning();
        if ($training_type_id>0){
            $TypeTable=TrainingType::find($training_type_id);
            $sanitized=$this->ParamsByTask($request,$sanitized,$TypeTable->table_link);
        }

        if ($training_type_id>0){

            switch ($TypeTable->table_link) {
                case  'training_number_letter':
                    $lastId = TrainingNumberLetter::create($sanitized)->id;
                    break;
                case  'training_words':

                    $sanitized['digit_number'] = $sanitized['digit_number_list'];
                    $sanitized['category'] =  $request->getCategory();

                    $lastId = TrainingWords::create($sanitized)->id;
                    break;
                case  'training_associative':
                    $lastId = TrainingAssociative::create($sanitized)->id;
                    break;
                case  'training_faces':
                    unset( $sanitized['category_list']);
                    unset( $sanitized['category']);
                    unset( $sanitized['range_type']);
                    if ($sanitized['level']=='learn'){
                        $sanitized['capability_faces'] =0;
                        $sanitized['gender'] = $request->getGender();
                    }else{
                        $sanitized['capability_faces'] = $request->getCapabilityFaces();
                    }
                    $sanitized['category_faces'] = $request->getCategoryFaces();

                    $lastId = TrainingFaces::create($sanitized)->id;
                    break;
                case  'training_cards':
                    $lastId = TrainingCards::create($sanitized)->id;
                    break;

//                case  'training_transform':
//
//                    $lastId = TrainingTransform::create($sanitized)->id;
//                    break;
                case  'training_memory':
                    unset($sanitized['capacity'] );
                    $lastId = TrainingMemory::create($sanitized)->id;
                  break;

                //------------ Maths
                case  'training_maths':
                    unset( $sanitized['category_list']);
                    unset( $sanitized['category']);
                    unset( $sanitized['range_type']);


                    $sanitized['category_maths'] = $request->getCategoryMaths();
                    $sanitized['capacity'] = $request->getCapacityMaths();

                    $sanitized['cnt_operation'] = $request->getCntOperaion();
                    $sanitized['div_action'] = $request->getDivAction();
                    $sanitized['procent_level'] = $request->getProcentLevel();
                    if ($sanitized['category_maths']=='percentage') {
                        $sanitized['level'] = 'practice';
                    }
                    if ($sanitized['category_maths']=='multiplication'){
                        $sanitized['comma_number'] = $request->getCapacity2Maths();
                    }elseif ($sanitized['category_maths']=='random'){
                        $sanitized['level']='practice';
                        $sanitized['cnt_operation'] = 1; //Одна операция

                        $sanitized['comma_number'] = $request->getCommaNubmer();
                        if (empty($sanitized['comma_number'] )) {
                            $sanitized['comma_number'] = 0;
                        }

                    }elseif ($sanitized['category_maths']=='division'){
                        $sanitized['capacity'] = $request->getDivCapacity();
                        $sanitized['comma_number'] =  $sanitized['div_comma'];
                        $sanitized['cnt_operation'] =  1;
                        $sanitized['procent_level'] =  'all';

                    } elseif ($sanitized['category_maths']=='fractions'){
                        $sanitized['level'] = 'practice';
                        $sanitized['comma_number'] = 2;
                        $sanitized['cnt_operation'] = 1; //Одна операция
                        $sanitized['div_action'] = $request->getFractionAction();

                    }else{
                        $sanitized['comma_number'] =  $sanitized['div_comma'];
                    }

                    if (empty($sanitized['cnt_operation'])){
                        $sanitized['cnt_operation'] =1;
                    }
                    if ( $sanitized['level']!=='practice' && $sanitized['level']!=='profi'){
                        $sanitized['level']= 'practice';
                    }

//                    print '<pre>';
//                    print_r($sanitized);
//                    die;
                    $lastId = TrainingMaths::create($sanitized)->id;
                    break;

                case  'training_abacus':

                    unset($sanitized['capacity'] );
                    unset($sanitized['capacity'] );
                    unset($sanitized['category_list'] );
                    unset($sanitized['capability_faces_list'] );
                    unset($sanitized['category_faces_list'] );
                    unset($sanitized['category_history_list'] );
                    unset($sanitized['pack_cards_list'] );
                    unset($sanitized['group_cards'] );
                    unset($sanitized['div_suits'] );
                    unset($sanitized['category'] );
                    unset($sanitized['suits_s'],$sanitized['suits_h'],$sanitized['suits_c'],$sanitized['suits_d'] );


                    $sanitized['interval'] = $request->getIntervals();

                    $sanitized['category_abacus']=$request->input('category_abacus_list')['value']??'all';
                    $sanitized['capacity_abacus']=$request->input('capacity_abacus_list')['value']??1;


                    if  ($sanitized['digit_number']>25){
                        $sanitized['digit_number']=25;
                    }

                    $lastId = TrainingAbacus::create($sanitized)->id;
                    break;
                case  'training_history':
                    unset($sanitized['capacity'] );
                    if ($sanitized['level']=='learn'){
                        $sanitized['level']='practice';
                    }
                    $sanitized['category_history'] = $request->getCategoryHistory();
                    $lastId = TrainingHistory::create($sanitized)->id;
                    break;
                case  'training_binnary':
                    unset($sanitized['capacity'] );


                    if ($sanitized['level']=='learn'){
                        $sanitized['digit_number']=8;
                    }
                    if ($sanitized['level']=='practice'){
                        $sanitized['show_groups']=$request->input('show_groups_list')['value']??2;
                        $sanitized['category_id']=0;
                        if  ($sanitized['digit_number']>=100){
                            $sanitized['digit_number']=100;
                        }
                    }
                    if ($sanitized['level']=='profi'){
                        $sanitized['show_groups']=$request->input('show_groups_list')['value']??2;
                        $sanitized['category_id']=$request->input('category_binary')['value']??2;
                        if  ($sanitized['digit_number']>=1000){
                            $sanitized['digit_number']=1000;
                        }
                    }

                    $lastId = TrainingBinnary::create($sanitized)->id;
                    break;
                case  'olympiad_number_letter':
                    $sanitized['level'] ='profi';
                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();
                    $sanitized['capacity'] = $request->getCapacity();

                    if ($sanitized['evaluation']=='practice'){
                        $sanitized['range_type'] ='increment';
                        $sanitized['range_value'] =$request->range_increment_olist['value']??'0-99';
                    }else{
                        $sanitized['range_value'] =null;
                        $sanitized['range_type'] =null;
                    }

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }


                    $lastId = OlympiadNumberLetter::create($sanitized)->id;
                    break;
                case  'olympiad_words':

                    $sanitized['level'] ='profi';
                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();
                    $sanitized['capacity'] = $request->getCapacity();
                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }

                    $lastId = OlympiadWords::create($sanitized)->id;
                    break;
                case  'olympiad_memory':
                    $sanitized['level'] ='profi';
                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }

                    $lastId = OlympiadMemory::create($sanitized)->id;
                    break;

                case  'olympiad_binnary':
                    $sanitized['level'] ='profi';
                    unset($sanitized['capacity'] );
                    unset($sanitized['capacity_list']);
                    $sanitized['category_id'] =$request->get('category_binary')['value']??1;
                    $sanitized['show_groups'] =$request->get('show_groups_list')['value']??0;
                    if   ($sanitized['category_id']==1 &&  $sanitized['show_groups']>2){
                        $sanitized['show_groups'] =2;
                    }

                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }
                    $lastId = OlympiadBinnary::create($sanitized)->id;
                    break;

                case  'olympiad_cards':
                    $sanitized['level'] ='profi';
                    unset($sanitized['capacity'] );
                    unset($sanitized['capacity_list']);

                    $sanitized['group_cards'] =$request->get('group_cards_list')['value']??1;
                    $sanitized['pack_cards'] =$request->get('pack_cards_list')['value']??1;

                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }


                    $lastId = OlympiadCards::create($sanitized)->id;
                    break;

                case  'olympiad_faces':
                    $sanitized['level'] ='profi';
                    unset($sanitized['capacity'] );
                    unset($sanitized['capacity_list']);

                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }
                    $lastId = OlympiadFaces::create($sanitized)->id;
                    break;
                case  'olympiad_history':
                    $sanitized['level'] ='profi';
                    unset($sanitized['capacity'] );
                    unset($sanitized['capacity_list']);

                    $sanitized['interval'] = $request->getIntervalsOlympiad();
                    $sanitized['interval_memory'] = $request->getIntervalsMemory();

                    if (empty( $sanitized['interval'] )){
                        $sanitized['interval'] =5*60;
                    }
                    if (empty( $sanitized['interval_memory'] )){
                        $sanitized['interval_memory'] =5*60;
                    }
                    $lastId = OlympiadHistory::create($sanitized)->id;
                    break;

                default:
                    return ['error' => 'Форма еще не готова'];

            }//end switch


            if ($lastId>0){
                if (!empty($listParams['is_private'])){
                    //---- Индивидуальное
                    $saveParams=[];

                    $saveParams['training_type_id']=(int)$training_type_id;
                    $saveParams['student_id']=(int)$listParams['student_id'];
                    $saveParams['training_id']=(int)$lastId;
                    $saveParams['date_start']=date('Y-m-d',strtotime($listParams['data']));
                    $taskId=$this->CreateTaskStudent($saveParams);
                }else{
                    //---- Груповое задание
                    $listParams['training_type_id']=(int)$training_type_id;
                    $listParams['training_id']=(int)$lastId;
                    $this->CreateTaskGroupStudent($listParams);
                }
            }

        }


        if ($request->ajax()) {
            return ['redirect' => url('admin/homework'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/homework');
    }
    public function DuplicateStudentTask(Request $request){
        $listParams=session()->get('homework');
        if (empty($listParams)){
            return redirect('/admin/calendar');
        }



        $move_date=$request->get('move_date');
        $now=strtotime("-1 days");


        if (strtotime($move_date)<$now){

            return response(['success'=>0,'message' =>_('admin.homework.error_copy_date')]);
        }

        if (!empty($listParams['is_private'])){
            $studentBreadCrumb=Student::where('id',$listParams['student_id'])->first();
            $listParams['group_id']=(int)$studentBreadCrumb['group_id'];
        }
        $dataFilterCheck=date('Y-m-d',strtotime($move_date));
        if (empty($listParams['is_private'])){
            $dataObj=StudentGroupTask::where('student_group_task.enabled',1)
                ->leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
                ->where('student_group_task.enabled', 1)
                ->where('date_start',$dataFilterCheck)
                ->where('group_id',(int)$listParams['group_id'])
                ->select('student_group_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link')->limit(1);
        }else{
            $dataObj=StudentTrainingTask::leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
                ->where('date_start',$dataFilterCheck)
                ->where('student_id',(int)$listParams['student_id'])
                ->select('student_training_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link')->limit(1);
        }
        $dataСheck=$dataObj->first();

        if (!empty($dataСheck['table_link'])){
            return response(['success'=>0,'message' =>'Ошибка, на дату '.$move_date.' есть задачи']);
        }

        $dataFilter=date('Y-m-d',strtotime($listParams['data']));

        if (empty($listParams['is_private'])){
            $dataObj=StudentGroupTask::where('student_group_task.enabled',1)
                ->leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
                ->where('student_group_task.enabled', 1)
                ->where('date_start',$dataFilter)
                ->where('group_id',(int)$listParams['group_id'])
                ->select('student_group_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link','student_group_task.group_id');
        }else{
            $dataObj=StudentTrainingTask::leftjoin('training_type', 'training_type.id', '=', 'training_type_id')
                ->where('date_start',$dataFilter)
                ->where('student_id',(int)$listParams['student_id'])
                ->select('student_training_task.id','training_id','training_type_id','training_type.name as training_name','training_type.table_link','student_training_task.group_id');
        }
        $data=$dataObj->get();
        $isOk=0;
        foreach ($data as $TaskList){
            //----------
            $lastId=0;
            if ($TaskList->training_type_id>0) {
                $modelTableClassTraning=TraningHelper::getModelByTable($TaskList->table_link);

                $paramsCopy = $modelTableClassTraning::find($TaskList->training_id);
                if (isset($paramsCopy)) {
                    $newPost = $paramsCopy->replicate();
                    $newPost->save();
                    $lastId = $newPost->id;
                }
            }
            if ($lastId>0){

                if (!empty($listParams['is_private'])) {
                    $createTaskCopy = StudentTrainingTask::find($TaskList->id);
                    $createTask = $createTaskCopy->replicate();
                    $createTask->date_start = date('Y-m-d',strtotime($move_date));
                    $createTask->training_id = (int)$lastId;//назначить новий
                    $createTask->cnt_repeat = 0;
                    $createTask->enabled = 1;
                    $createTask->is_done = 0;
                    $createTask->is_self = 0;
                    $createTask->save();
                    $isOk=1;
                }else{
                    $listParamsClone=[];
                    $listParamsClone['group_id']=$TaskList->group_id;
                    $listParamsClone['date_start']= $listParamsClone['data']=date('Y-m-d',strtotime($move_date));
                    $listParamsClone['training_type_id']=$TaskList->training_type_id;
                    $listParamsClone['training_id']=(int)$lastId;
                    $this->CreateTaskGroupStudent($listParamsClone);
                    $isOk=1;
                }
            }
        }

        if ($request->ajax() ) {

            if ($isOk){
                $listParams['data']=date('Y-m-d',strtotime($move_date));
                session()->put('homework', $listParams);
                return response(['success'=>1,'message' => trans('admin.operation.succeeded')]);
            }else{
                return response(['success'=>0,'message' => 'Errors']);
            }

        }

        return redirect()->back();
    }
    //---Создает задачу для студента, для само-теста
    private function CreateTaskStudent($saveParams){
        $createTask = new StudentTrainingTask();

        $createTask->date_start = $saveParams['date_start'];//date('Y-m-d', time());
        $createTask->training_type_id = (int)$saveParams['training_type_id'];
        $createTask->training_id =(int)$saveParams['training_id'];
        $createTask->student_id = (int)$saveParams['student_id'];
        $createTask->cnt_repeat = 0;
        $createTask->group_id=0;
        $createTask->is_done = 0;
        $createTask->is_self = 0;
        $createTask->enabled = 1;
        $createTask->save();
        $TaskId = $createTask->id;
        return $TaskId;
    }

    private function CreateTaskGroupStudent($saveParams){
        $createTask=new StudentGroupTask();
        $createTask->group_id=(int)$saveParams['group_id'];
        $createTask->date_start=date('Y-m-d',strtotime($saveParams['data']));
        $createTask->training_type_id=(int)$saveParams['training_type_id'];
        $createTask->training_id=(int)$saveParams['training_id'];
        $createTask->enabled=1;
        $createTask->save();
        $createTask->AssignTask($createTask->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Franchisee $franchisee
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function edit($id)
    {
        $id=(int)$id;
        $listParams=session()->get('homework');
        if (empty($listParams)){
            return redirect('/admin/calendar');
        }


        $trainingType=$trainingTypes = TrainingType::where('enabled',1)->get();

        $ViewParams['capacity'] = SiteHelper::getCapacityArray();
        $ViewParams['capacity_olypiad']=SiteHelper::getCapacityOlimpiadArray();
        $ViewParams['intervals_memory']=SiteHelper::getIntervalsOlimpiadMemory();
        $ViewParams['digit_number']= SiteHelper::getDigitNumberArray();
        $ViewParams['digitIntervals']=SiteHelper::getDigitIntervalsArray();
        $ViewParams['digitIncrements']= SiteHelper::getDigitIncrementsArray();
        $ViewParams['intervals']= SiteHelper::getIntervalsArray();
        $ViewParams['repeat_number']=  SiteHelper::getRepeatNumberArray();
        $ViewParams['сategory_list'] = SiteHelper::getCategoryArray();
        $ViewParams['intervals_olimpiad'] = SiteHelper::getIntervalsOlimpiadShow();
        $ViewParams['evaluation'] ='practice';


        //-------- Maths begin
        $ViewParams['сategoryMaths'] =    $ViewParams['сategory_maths_list']= SiteHelper::getCategoryMaths();
        $ViewParams['capacity_maths_list'] = SiteHelper::getCapacityMathsArray();
        $ViewParams['capacity_maths_list2'] = SiteHelper::getCapacity2MathsArray();
        $ViewParams['div_action'] = SiteHelper::getDivActionArray();
        $ViewParams['div_capacity_list'] = SiteHelper::getDivCapacity();
        $ViewParams['procent_level_list'] = SiteHelper::getProcentsLevelArray();
        $ViewParams['cnt_operation_list'] = SiteHelper::getCntOperaion();
        $ViewParams['comma_number_list'] = SiteHelper::getCommaNumber();
        $ViewParams['div_action_fraction_list'] = SiteHelper::getDivActionFraction();
        //-------- Maths end

        //-------- faces begin
        $ViewParams['category_faces_list'] = SiteHelper::getCategoryFacesArray();
        $ViewParams['capability_faces_list'] = SiteHelper::getCapabilityFacesArray();
        $ViewParams['gender_list'] = SiteHelper::getGenderFacesArray();

        //-------- faces begin

        //-------- cards begin
        $ViewParams['group_cards_list'] = SiteHelper::getCardGroups();
        $ViewParams['pack_cards_list'] = SiteHelper::getCardPack();
        //-------- cards end

        //-------- history begin
        $ViewParams['category_history_list'] = SiteHelper::getCategoryHistory();
       // $ViewParams['capability_faces_list'] = SiteHelper::getCapabilityFacesArray();
        //$ViewParams['gender_list'] = SiteHelper::getGenderFacesArray();

        //-------- history end

        //-------- binnary begin

        //-------- binnary end
        if (empty($listParams['is_private'])){
            $TrainingTask =StudentGroupTask::where('id', (int)$id)->first();
        }else{
            $TrainingTask =StudentTrainingTask::where('id',(int) $id)->first();
        }


        if (!empty($TrainingTask)) {
            $tableName='';
            $FindTable = $TrainingTask->TrainingType;


            if (!empty($FindTable)){
                $tableName = $FindTable->table_link;
            }


            if ($tableName=='training_number_letter'){
                $trainingParams = TrainingNumberLetter::find($TrainingTask->training_id );

                $capacity_id=$trainingParams->capacity??0;

                $range_value_id=$trainingParams->range_value;

                $trainingParams->capacity_list=SiteHelper::getCapacityArray($capacity_id);
                $trainingParams->digit_number_list =$trainingParams->digit_number;
                if ($trainingParams->range_type=='even'){
                    $trainingParams->range_even_list=json_decode($range_value_id);
                }else{
                    $trainingParams->range_increment_list=SiteHelper::getDigitIncrementsArray($range_value_id);//????????
                }

            }
            if ($tableName=='training_memory'){
                $trainingParams = TrainingMemory::find($TrainingTask->training_id );
            }
            if ($tableName=='training_words'){
                $trainingParams = TrainingWords::find($TrainingTask->training_id );
            }
            if ($tableName=='training_transform'){
                $trainingParams = TrainingTransform::find($TrainingTask->training_id );
            }
            if ($tableName=='training_associative'){
                $trainingParams = TrainingAssociative::find($TrainingTask->training_id );
            }

            if ($tableName=='training_faces'){
                $trainingParams = TrainingFaces::find($TrainingTask->training_id );
                $trainingParams->capability_faces_list=SiteHelper::getCapabilityFacesArray($trainingParams->capability_faces);
                $trainingParams->category_faces_list=SiteHelper::getCategoryFacesArray($trainingParams->category_faces);
                $trainingParams->gender_list=SiteHelper::getGenderFacesArray($trainingParams->gender);
            }

            if ($tableName=='training_cards'){
                $trainingParams = TrainingCards::find($TrainingTask->training_id );
                //------ Cards Default
                if (empty($trainingParams->suits)){
                    $trainingParams->suits_s=1;
                    $trainingParams->suits_h=1;
                    $trainingParams->suits_c=1;
                    $trainingParams->suits_d=1;
                    $suidsDB=['H','S','C','D'];
                }else{
                    $suidsDB=explode(',',$trainingParams->suits);
                }
                $det_suits=['S','H','C','D'];

                foreach ($det_suits as $suit){
                    $suitSet=strtolower($suit);
                    if (in_array($suit,$suidsDB)){
                        $trainingParams['suits_'.$suitSet]=1;
                    }else{
                        $trainingParams['suits_'.$suitSet]=0;
                    }
                }


                $trainingParams->div_suits=$trainingParams->div_suits??1;
                $trainingParams->group_cards=$trainingParams->group_cards??1;
                $trainingParams->group_cards_list=SiteHelper::getCardGroups($trainingParams->group_cards);
                $trainingParams->pack_cards=$trainingParams->pack_cards??1;
                $trainingParams->pack_cards_list=SiteHelper::getCardGroups($trainingParams->pack_cards);


            }
            if ($tableName=='training_maths'){

                $trainingParams = TrainingMaths::find($TrainingTask->training_id );

                $category_maths='random';
                if (!empty($trainingParams->category_maths)){
                    $category_maths=$trainingParams->category_maths;
                }
                $trainingParams->category_maths_list=SiteHelper::getCategoryMaths($category_maths);
                $trainingParams->capacity_maths_list=SiteHelper::getCapacityMathsArray($trainingParams->capacity);
                if ($category_maths=='multiplication'){
                    $trainingParams->capacity_maths_list2=SiteHelper::getCapacity2MathsArray($trainingParams->comma_number);
                }
                $trainingParams->div_action_list=SiteHelper::getDivActionArray($trainingParams->div_action);
                $trainingParams->procent_level_list=SiteHelper::getProcentsLevelArray($trainingParams->procent_level);
                $trainingParams->div_capacity_list=SiteHelper::getDivCapacity($trainingParams->capacity);
                $trainingParams->div_fraction_list=SiteHelper::getDivActionFraction($trainingParams->div_action);
                $trainingParams->interval_list=SiteHelper::getIntervalsArray($trainingParams->interval);
                $trainingParams->comma_number_list=SiteHelper::getCommaNumber($trainingParams->comma_number);
                $trainingParams->cnt_operation_list=SiteHelper::getCntOperaion($trainingParams->cnt_operation);
                $trainingParams->div_comma=$trainingParams->comma_number;


            }

            if ($tableName=='training_history'){
                $trainingParams = TrainingHistory::find($TrainingTask->training_id );


                $trainingParams->digit_number_list =$trainingParams->digit_number ??10;
                if ($trainingParams->range_type=='even'){
                    $trainingParams->range_even_history_list=json_decode($trainingParams->range_value);
                }else{
                    $trainingParams->range_increment_list=['value'=>$trainingParams->range_value];
                }

                $trainingParams->category_history_list=SiteHelper::getCategoryHistory($trainingParams->category_history);

            }
            if ($tableName=='training_binnary'){
                $trainingParams = TrainingBinnary::find($TrainingTask->training_id );

                if ($trainingParams->level=='profi'){
                    if ($trainingParams->category_id==2 && $trainingParams->show_groups==1){
                        $trainingParams->show_groups_list=['value'=>$trainingParams->show_groups,'label'=>   trans('student.binnary.form_elements.not_group')];
                    }else{
                        $trainingParams->show_groups_list=['value'=>$trainingParams->show_groups,'label'=>$trainingParams->show_groups];
                    }



                    $trainingParams->category_binary=SiteHelper::getBinnaryCatergory($trainingParams->category_id);
                    $trainingParams->categoryBinaryFlag=$trainingParams->category_id;
                }
                if (empty($trainingParams->show_groups_list)) {
                    if ($trainingParams->show_groups == 1) {
                        $trainingParams->show_groups_list = ['value' => $trainingParams->show_groups, 'label' => trans('student.binnary.form_elements.not_group')];
                    } else {
                        $trainingParams->show_groups_list = ['value' => $trainingParams->show_groups, 'label' => $trainingParams->show_groups];
                    }
                }
            }
            if ($tableName=='training_abacus'){
                $trainingParams = TrainingAbacus::find($TrainingTask->training_id );
                $trainingParams->capacity_abacus_list=['value'=>$trainingParams->capacity_abacus,'label'=>$trainingParams->capacity_abacus];
                $operation_list['all'] = ['label'=>__('student.abacus.form_elements.operation_type.all'),'value'=>'all'];
                $operation_list['+'] = ['label'=>__('student.abacus.form_elements.operation_type.addition'),'value'=>'+'];
                $operation_list['-'] = ['label'=>__('student.abacus.form_elements.operation_type.subtraction'),'value'=>'-'];


                if (isset($operation_list[$trainingParams->category_abacus])) {
                    $trainingParams->category_abacus_list = $operation_list[$trainingParams->category_abacus];
                } else {
                    $trainingParams->category_abacus_list = $operation_list['all'];
                }

            }

            if ($tableName=='olympiad_number_letter'){
                $trainingParams = OlympiadNumberLetter::find($TrainingTask->training_id );

                if (!empty($trainingParams->capacity)){
                    $trainingParams->capacity_list=SiteHelper::getCapacityOlimpiadArray($trainingParams->capacity);
                }

                if ($trainingParams->evaluation=='practice'){
                    $trainingParams->range_increment_olist=array('key' => 0, 'value' => $trainingParams->range_value);
                }

            }
            if ($tableName=='olympiad_words'){
                $trainingParams = OlympiadWords::find($TrainingTask->training_id );

                $trainingParams->interval_memory_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval_memory);
                $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
            }
            if ($tableName=='olympiad_memory'){
                $trainingParams = OlympiadMemory::find($TrainingTask->training_id );

                if (!empty($trainingParams->interval_memory)){
                    $trainingParams->interval_memory_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval_memory);
                }
                if (!empty($trainingParams->interval)){
                    $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
                }

            }
            if ($tableName=='olympiad_binnary'){
                $trainingParams = OlympiadBinnary::find($TrainingTask->training_id );

                if ($trainingParams->category_id==2 && $trainingParams->show_groups==1){
                    $trainingParams->show_groups_list=['value'=>$trainingParams->show_groups,'label'=>$trainingParams->show_groups];

                }else{
                    $trainingParams->show_groups_list=['value'=>1,'label'=>   trans('student.binnary.form_elements.not_group')];
                }


                $trainingParams->category_binary=SiteHelper::getBinnaryCatergory($trainingParams->category_id);
                $trainingParams->categoryBinaryFlag=$trainingParams->category_id;

                if (!empty($trainingParams->interval_memory)){
                    $trainingParams->interval_memory_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval_memory);
                }
                if (!empty($trainingParams->interval)){
                    $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
                }

            }

            if ($tableName=='olympiad_cards'){
                $trainingParams = OlympiadCards::find($TrainingTask->training_id );

                $trainingParams->group_cards_list =SiteHelper::getCardGroups($trainingParams->group_cards);
                $trainingParams->pack_cards_list =SiteHelper::getCardPack($trainingParams->pack_cards);

                if (!empty($trainingParams->interval_memory)){
                    $trainingParams->interval_memory_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval_memory);
                }
                if (!empty($trainingParams->interval)){
                    $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
                }
            }


            if ($tableName=='olympiad_faces'){
                $trainingParams = OlympiadFaces::find($TrainingTask->training_id );
                if (!empty($trainingParams->interval)){
                    $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
                }
            }
            if ($tableName=='olympiad_history'){
                $trainingParams = OlympiadHistory::find($TrainingTask->training_id );
                if (!empty($trainingParams->interval)){
                    $trainingParams->interval_olimpiad_list=SiteHelper::getIntervalsOlimpiadShow($trainingParams->interval);
                }
            }

            $trainingParams->training_type_id = $FindTable;
            $trainingParams->digit_number_list =$trainingParams->digit_number;

            if ($tableName!='training_maths' && $tableName!='olympiad_words' ){
                $trainingParams->interval_list=SiteHelper::getIntervalsArray($trainingParams->interval);
            }

            $trainingParams->repeat_number_list=SiteHelper::getRepeatNumberArray($trainingParams->repeat_number);
            $trainingParams->сategory_list=SiteHelper::getCategoryArray($trainingParams->category);
        }

        $ViewParams['trainingParams']=$trainingParams;
        $ViewParams['trainingType']=$trainingType;
        $ViewParams['trainingTypes']=$trainingTypes;

        return view('admin.homework.edit',$ViewParams);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHomework $request
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateHomework $request, $link_traning_id=0) //
    {

        $listParams=session()->get('homework');
        if (empty($listParams)){
            return redirect('/admin/calendar');
        }
        $link_traning_id=(int)$link_traning_id;
        $sanitized = $request->getSanitized();

        $training_type_id =(int) $request->getTypeTraning();
        if ($training_type_id>0){
            $TypeTable=TrainingType::find($training_type_id);
            $sanitized=$this->ParamsByTask($request,$sanitized,$TypeTable->table_link);
            $ObjectParamsTable=TraningHelper::getModelByTable($TypeTable->table_link);
            if (!empty($ObjectParamsTable)){
                $TraningObj= $ObjectParamsTable::find($link_traning_id);
                $TraningObj->update($sanitized);

            }
        }

        if ($request->ajax()) {
            if (isset($TraningObj) && ($TraningObj->id>0)){
                return [
                    'redirect' => url('admin/homework'),
                    'message' => trans('admin.operation.succeeded'),
                ];
            }else{
                return [
                    //'redirect' => url('admin/homework'),
                    'message' => trans('admin.operation.error'),
                ];
            }

        }

        return redirect('admin/homework');
    }

    public function DeleteAll(DestroyHomework $request) { //только для груповых
        $listParams=session()->get('homework');
        if (!empty($listParams) && $listParams['group_id']>0){
            $findDate=date('Y-m-d',strtotime($listParams['data']));
            $listTaskGroup=StudentGroupTask::where('date_start',$findDate)->where('group_id',(int)$listParams['group_id'])->get();
            foreach ($listTaskGroup as $TaskGroup  ){
                StudentGroupTask::DeleteGroup($TaskGroup->id);
            }
            StudentGroupTask::where('date_start',$findDate)->where('group_id',(int)$listParams['group_id'])->delete();
        }
        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }
        //return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyHomework $request
     * @param Franchisee $franchisee
     * @return ResponseFactory|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyHomework $request, $id)
    {
        $id=(int)$id;
        $listParams=session()->get('homework');
        $listParams['is_private']=(int)$listParams['is_private']??0;


        if (!empty($listParams['is_private'])){
            $TrainingTask=StudentTrainingTask::find($id);
            if (!empty($TrainingTask) && $TrainingTask['student_id']==$listParams['student_id']) {
                $FindTable = $TrainingTask->TrainingType;
                $tableName = $FindTable->table_link;

                if (!empty($tableName)) {
                    DB::table($tableName)->where("id", $TrainingTask->training_id)->delete();
                    $TrainingTask->delete();
                }
            }
        }else{
            $TrainingTask= StudentGroupTask::find($id) ;
            if (isset($TrainingTask)) {
                $FindTable=$TrainingTask->TrainingType;
                $tableName=$FindTable->table_link;

                if (!empty($tableName)){
                    DB::table($tableName)->where("id", $TrainingTask->training_id)->delete();
                    $TrainingTask->DeleteGroup($id);
                    $TrainingTask->delete();
                }
            }
        }


        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

}
