<?php

namespace App\Http\Requests\Admin\HomeWork;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Factory as ValidationFactory;


class StoreOlympiad extends FormRequest
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

      //  $validationFactory->extend('rangeValueRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Виберіть range');
       // $validationFactory->extend('capacityRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Виберіть capacity');
      //  $validationFactory->extend('countDigitRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле countDigit є обов\'язковим для заповнення.');
      //  $validationFactory->extend('intervalsRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле interval є обов\'язковим для заповнення.');
       // $validationFactory->extend('repeatCountRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['value']) && !empty($value['value'])){$ret=true;}return $ret; }, 'Поле epeatCount є обов\'язковим для заповнення.');
       $validationFactory->extend('trainingTypeRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Поле Тип тренировки є обов\'язковим для заповнення.');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //'level' => ['required', 'string'],
            'training_type_id' =>  ['required','trainingTypeRule'],
            'evaluation' =>  ['required'],
            'digit_number' =>  ['int'],
            'capacity_list' =>  ['sometimes'],
            'category_binary' =>  ['sometimes'],
            'show_groups_list' =>  ['sometimes'],
           'interval_list' =>  ['sometimes'],
           'interval_memory' =>  ['sometimes'],
            'repeat_number_list' => ['sometimes'],//repeatCountRule
        ];
    }


    /**
    * Modify input data
    *
    * @return array
    */


    public function getTypeTraning(){
        if ($this->has('training_type_id')){
            return @$this->get('training_type_id')['id'];
        }
        return null;
    }
    public function getSanitized(): array{

        $sanitized = $this->validated();
        return $sanitized;
    }

    public function getCapacity(){
        if ($this->has('capacity_list')){
            return @$this->get('capacity_list')['value'];
        }
        return null;
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
    public function getIntervalsMemory(){
        if ($this->has('interval_memory_list')){
            return @$this->get('interval_memory_list')['value'];
        }
        return null;
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
            return @$this->get('range_increment_list')['value'];
        }
        return null;
    }
    public function getCategory(){
        if ($this->has('сategory_list')){
            return @$this->get('сategory_list')['value'];
        }
        return null;
    }
}
