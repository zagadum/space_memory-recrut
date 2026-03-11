<?php

namespace App\Helpers;
use App\Models\OlympiadBinnary;
use App\Models\OlympiadNumberLetter;
use App\Models\OlympiadWords;
use App\Models\OlympiadMemory;
use App\Models\OlympiadFaces;
use App\Models\StudentTrainingTask;
use App\Models\TrainingFaces;
use App\Models\TrainingNumberLetter;
use App\Models\TrainingType;
use App\Models\TrainingWords;
use App\Models\TrainingMemory;

use App\Models\TrainingAssociative;
use App\Models\TrainingMaths;
use App\Models\TrainingCards;
use App\Models\TrainingAbacus;
use App\Models\TrainingHistory;
use App\Models\TrainingBinnary;
use App\Models\OlympiadCards;
use App\Models\OlympiadHistory;
use App\Models\TraningOnline;

class TraningHelper {

    public static function getModelByTable($table_link){
            $modelMap = [
            'training_number_letter' => TrainingNumberLetter::class,
            'training_words' => TrainingWords::class,
            'training_associative' => TrainingAssociative::class,
            'training_faces' => TrainingFaces::class,
            'training_cards' => TrainingCards::class,
            'training_memory' => TrainingMemory::class,
            'training_maths' => TrainingMaths::class,
            'training_abacus' => TrainingAbacus::class,
            'training_history' => TrainingHistory::class,
            'training_binnary' => TrainingBinnary::class,
            'olympiad_number_letter' => OlympiadNumberLetter::class,
            'olympiad_words' => OlympiadWords::class,
            'olympiad_memory' => OlympiadMemory::class,
            'olympiad_binnary' => OlympiadBinnary::class,
            'olympiad_cards' => OlympiadCards::class,
            'olympiad_faces' => OlympiadFaces::class,
            'olympiad_history' => OlympiadHistory::class,
        ];

            if (isset($modelMap[$table_link])) {
                 return $modelMap[$table_link];
             }else{
                 return null;
             }

    }
    public static function TraningParams($id) {
        $TrainingTaskParams=[];

        $task_id=0;
        $TrainingTask=StudentTrainingTask::where('id', $id)->first();

        $TypeTable['table_link']='none';

        if (!empty($TrainingTask)){
            $task_id=$TrainingTask['training_id'];
            $TypeTable = TrainingType::find($TrainingTask['training_type_id'])->toArray();

            $modelTableClassTraning=TraningHelper::getModelByTable($TypeTable['table_link']);
            if (!is_null($modelTableClassTraning)){
                $TrainingTaskParams=$modelTableClassTraning::where('id',$task_id)->first();
            }
            if ($TypeTable['olympiad']==1){
                $TrainingTaskParams['level']='profi';
            }
        }


        return ['task_id'=>$task_id,'TrainingTaskParams'=>$TrainingTaskParams,'TrainingTask'=>$TrainingTask,'is_olympiad'=>$TypeTable['olympiad'],'redirect'=>self::RedirectType($TypeTable['table_link'],$TypeTable['olympiad'])];
    }

    public static function RedirectType($table_link='',$is_olympiad=0) {
        $redirectModule=str_replace('training_','',$table_link);
        $redirectModule=str_replace('olympiad_','',$redirectModule);
        $module=str_replace('_','-',$redirectModule);
        if ($is_olympiad==0){
            $logic='traning';
        }else{
            $logic='olympiad';
        }
        return $logic.'/'.$module;
    }
    public static function Start($table_link='',$task_id=-1) {
        if ($task_id==-1){
            $task_id=time();
        }
        $user_id=auth()->id();
        $role=session('role') ?? 'none';
        $anyStart = TraningOnline::where('user_id', $user_id)->where('role',$role)->first();
        if (!empty($anyStart)){
            die('IS START');
        }else{
            TraningOnline::create([
                'user_id'    => $user_id,
                'role'       => $role,
                'task_id'    => $task_id,
                'table_link' => $table_link,
                'url'        => request()->url(),
                'start_time' => now(),
                'finish_time'=> null,
            ]);
        }

    }

}
