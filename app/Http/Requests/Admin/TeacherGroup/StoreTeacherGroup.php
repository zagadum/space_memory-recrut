<?php

namespace App\Http\Requests\Admin\TeacherGroup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

class StoreTeacherGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
     //   return Gate::allows('admin.teacher-group.create');
    }
    public function __construct(ValidationFactory $validationFactory) {

        $validationFactory->extend('franchiseeRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Франшиза');
        $validationFactory->extend('teacherRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Вчителя групи');
        $validationFactory->extend('ageRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Вік групи');

    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [

            'franchisee' => ['required','franchiseeRule'],
            'teacher' => ['required','teacherRule'],
            'age' => ['required','ageRule'],

            'franchisee_id' => ['nullable'],

            'teacher_id' => ['nullable'],
            'country_id' => ['sometimes'],
            'region_id' => ['sometimes'],
            'city_id' => ['sometimes'],
            //'group_id' => ['nullable', 'integer'],
            'name' => ['required', 'string'],
          //  'age_id' => ['nullable', 'integer'],
            'address' => ['string'],
            'locations' => ['nullable', 'string'],
            'start_time'=>['sometimes']
           // 'start_day' => ['required', 'string'],
            //'workday' => ['required', 'string'],
          //  'enabled' => ['required', 'boolean'],

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

        //Add your code for manipulation with request data here

        return $sanitized;
    }

    public function getFranchiseeId(){
        if ($this->has('franchisee')){
            return @$this->get('franchisee')['id'];
        }
        return null;
    }
    public function getTeacherId(){
        if ($this->has('teacher')){
            return @$this->get('teacher')['id'];
        }
        return null;
    }
    public function getCountryId(){
        if ($this->has('country')){
            return @$this->get('country')['id'];
        }
        return null;
    }

    public function getRegionId(){
        if ($this->has('region')){
            return @$this->get('region')['id'];
        }
        return null;
    }
    public function getCityId(){
        if ($this->has('city')){
            return @$this->get('city')['id'];
        }
        return null;
    }
    public function getAgeId(){
        if ($this->has('age')){
            return @$this->get('age')['id'];
        }
        return null;
    }
}
