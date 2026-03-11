<?php

namespace App\Http\Requests\Admin\HomeWork;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Factory as ValidationFactory;


class StoreHomework extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
       // return Gate::allows('admin.franchisee.create');
    }
    public function __construct(ValidationFactory $validationFactory) {

        $validationFactory->extend('rangeValueRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Виберіть range');
        $validationFactory->extend('capacityRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Виберіть capacity');
        $validationFactory->extend('countDigitRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле countDigit є обов\'язковим для заповнення.');
        $validationFactory->extend('intervalsRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле interval є обов\'язковим для заповнення.');
        $validationFactory->extend('repeatCountRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле epeatCount є обов\'язковим для заповнення.');
        $validationFactory->extend('trainingTypeRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Поле Тип тренировки є обов\'язковим для заповнення.');
        $validationFactory->extend('CategoryRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле CategoryRule є обов\'язковим для заповнення.');

    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'level' => ['required', 'string'],
            'training_type_id' =>  ['required','trainingTypeRule'],
            'range_type' => ['sometimes'],
            'range_value_list' =>  ['sometimes','rangeValueRule'],
            'category_list' =>  ['sometimes'],
            'is_present' =>  ['sometimes'],
            'cnt_operation_list' =>  ['sometimes'],
            'category_maths_list' =>  ['sometimes'],
            'capacity_list' =>  ['sometimes'],
            'interval_olimpiad_list' =>  ['sometimes'],
            'comma_number_list' =>  ['sometimes'],
            'capacity_maths_list' =>  ['sometimes'],
            'capacity_maths_list2' =>  ['sometimes'],
           'div_action_list' =>  ['sometimes'],
           'div_fraction_list' =>  ['sometimes'],
           'div_capacity_list' =>  ['sometimes'],
           'procent_level_list' =>  ['sometimes'],
           'div_comma' =>  ['sometimes'],
           'comma_number' =>  ['sometimes'],
            'evaluation' =>  ['sometimes'],
            'digit_number_list' =>  ['sometimes'],
            'range_increment_list' =>  ['sometimes'],
            'interval_list' =>  ['sometimes','intervalsRule'],
            'interval_delta' =>  ['sometimes'],
            'interval_memory_list' =>  ['sometimes'],
           'capability_faces_list' =>  ['sometimes'],
           'category_faces_list' =>  ['sometimes'],
           'category_history_list' =>  ['sometimes'],
            'gender_list' => ['sometimes'],
            'repeat_number_list' => ['sometimes'],
             'suits_s' => ['sometimes'],
            'suits_h' => ['sometimes'],
            'suits_d' => ['sometimes'],
            'suits_c' => ['sometimes'],
            'div_suits' => ['sometimes'],
            'group_cards' => ['sometimes'],
            'pack_cards_list' => ['sometimes'],
            'range_even_history_list' => ['sometimes'],
            'range_increment_olist' => ['sometimes'],
            'category_abacus_list' => ['sometimes'],
            'capacity_abacus_list' => ['sometimes'],

        ];
    }


    /**
    * Modify input data
    *
    * @return array
    */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();
        return $sanitized;
    }

    public function getTypeTraning(){
        if ($this->has('training_type_id')){
            return @$this->get('training_type_id')['id'];
        }
        return null;
    }

    public function getCapacity(){
        if ($this->has('capacity_list')){
            return @$this->get('capacity_list')['value'];
        }
        return null;
    }
    public function getCategory(){
        if ($this->has('сategory_list')){
            return @$this->get('сategory_list')['value'];
        }
        return 'noun-noun';
    }
    public function getCommaNubmer(){
        if ($this->has('comma_number_list')){
            return @$this->get('comma_number_list')['value'];
        }
        return null;
    }
    public function getDivAction(){
        if ($this->has('div_action_list')){
            return @$this->get('div_action_list')['value'];
        }
        return 'all';
    }
    public function getFractionAction(){
        if ($this->has('div_fraction_list')){
            return @$this->get('div_fraction_list')['value'];
        }
        return 'all';
    }

    public function getCntOperaion(){
        if ($this->has('cnt_operation_list')){
            return @$this->get('cnt_operation_list')['value'];
        }
        return 1;
    }
    public function getProcentLevel(){
        if ($this->has('procent_level_list')){
            return @$this->get('procent_level_list')['value'];
        }
        return null;
    }



    public function getCategoryMaths(){
        if ($this->has('category_maths_list')){
            return @$this->get('category_maths_list')['value'];
        }
        return null;
    }


    public function getCapacityFaces(){
        if ($this->has('capability_faces_list')){
            return @$this->get('capability_faces_list')['value'];
        }
        return 0;
    }

    public function getCapacityMaths(){
        if ($this->has('capacity_maths_list')){
            return @$this->get('capacity_maths_list')['value'];
        }
        return 'random';
    }
    public function getCapacity2Maths(){
        if ($this->has('capacity_maths_list2')){
            return @$this->get('capacity_maths_list2')['value'];
        }
        return 1;
    }
    public function getDivCapacity(){
        if ($this->has('div_capacity_list')){
            return @$this->get('div_capacity_list')['value'];
        }
        return 0;
    }

    public function getCountDigit(){
        if ($this->has('digit_number_list')){
            return @$this->get('digit_number_list')['value'];
        }
        return null;
    }

    public function getIntervals(){
        if ($this->has('interval_list')){
            return @$this->get('interval_list')['value'];
        }
        return null;
    }


    public function getLanguageValue(){

        if ($this->has('languages_list')){
            $lang = @$this->get('languages_list')['value'];
            return @$this->get('languages_list')['value'];
        }
        return null;
    }
    public function getLanguagesValues(){

        if ($this->has('languages_list')){
            $lang = @$this->get('languages_list')['value'];
            return @$this->get('languages_list')['value'];
        }
        return null;
    }

    public function getIntervalsOlympiad(){
        if ($this->has('interval_olimpiad_list')){
            return @$this->get('interval_olimpiad_list')['value'];
        }
        return null;
    }
    public function getCapabilityFaces(){
        if ($this->has('capability_faces_list')){
            return @$this->get('capability_faces_list')['value'];
        }
        return 0;
    }
    public function getGender(){
        if ($this->has('gender_list')){
            return @$this->get('gender_list')['value'];
        }
        return 0;
    }
  public function getCategoryFaces(){
        if ($this->has('category_faces_list')){
            return @$this->get('category_faces_list')['value'];
        }
        return 0;
    }
    public function getCategoryHistory(){
        if ($this->has('category_history_list')){
            return @$this->get('category_history_list')['value'];
        }
        return 1;
    }
    public function getIntervalsMemory(){
        if ($this->has('interval_memory_list')){
            return @$this->get('interval_memory_list')['value'];
        }
        return 0;
    }
    public function getRepeatCount(){
        if ($this->has('repeat_number_list')){
            return @$this->get('repeat_number_list')['value'];
        }
        return null;
    }

    public function getRangeEventValue(){
        if ($this->has('range_even_list')){
            return @$this->get('range_even_list');
        }
        return null;
    }
    public function getRangeIncrementValue(){
        if ($this->has('range_increment_list')){
            if (isset($this->get('range_increment_list')['value'])){
                return  $this->get('range_increment_list')['value'];
            }
            if (isset($this->get('range_increment_list')[0]['value'])){
                return  $this->get('range_increment_list')[0]['value'];
            }
        }
        return null;
    }
    public function getSuits(){
        $det_suits=['S','H','C','D'];
        $suitsSelect=[];

        foreach ($det_suits as $suit){
            $suit_sm=strtolower($suit);
            if ($this->has('suits_'.$suit_sm)){
                 $valSuits=(int)$this->get('suits_'.$suit_sm);
            }
            if (!empty($valSuits)){
                $suitsSelect[]=$suit;
            }
        }
        if (empty($suitsSelect)){
            $suitsSelect=['S','H','C','D'];
        }

       $res= implode(',',$suitsSelect);

        return $res;
    }
    public function getDivSuits(){
        if ($this->has('div_suits_list')){
            return @$this->get('div_suits_list')['value'];
        }
        return null;
    }
    public function getGroupCards(){
        if ($this->has('group_cards_list')){
            return @$this->get('group_cards_list')['value'];
        }
        return null;
    }
    public function getPackCards(){
        if ($this->has('pack_cards_list')){
            return @$this->get('pack_cards_list')['value'];
        }
        return null;
    }

}
