<?php

namespace App\Http\Requests\Admin\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

class UpdateStudent extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
        //return Gate::allows('admin.student.edit', $this->student);
    }

    public function __construct(ValidationFactory $validationFactory) {

        $validationFactory->extend('franchiseeRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Франшиза');
        $validationFactory->extend('teacherRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Вчителя групи');
        $validationFactory->extend('groupRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Групу');


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
            'groupSet' => ['required','groupRule'],

          //  'group_id' => ['sometimes', 'integer'],
         //   'teacher_id' => ['sometimes', 'integer'],
            'email' => ['required', 'email', 'string'],
            'subcribe_email' => ['nullable', 'email'],
            'password' => ['nullable',  'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],
            'surname' => ['nullable', 'string'],
            'lastname' => ['nullable', 'string'],
            'patronymic' => ['nullable', 'string'],

            'parent1_surname' => ['required', 'string'],
            'parent1_lastname' => ['required', 'string'],
            'parent1_patronymic' => ['nullable', 'string'],

            'phone1Save' => ['nullable', 'array'],
            'parent2_surname' => ['nullable','string'],
            'parent2_first_name' => ['nullable', 'string'],
            'parent2_patronymic' => [ 'nullable','string'],

            'phone2Save' => ['nullable', 'array'],

            'parent3_surname' => ['nullable','string'],
            'parent3_first_name' => [ 'nullable','string'],
            'parent3_patronymic' => ['nullable', 'string'],
            'dob' => ['nullable', 'string'],
            'phoneSave' => ['nullable', 'array'],

            'start_day' => ['nullable', 'date'],
            'date_finish' => ['nullable', 'date'],
            //'sum_aboniment' => ['sometimes', 'integer'],
            'discount' => ['nullable' ],
            //'balance' => ['sometimes', 'integer'],
            'language' => ['required', 'string'],
            'is_twochildren' => [ 'boolean'],
            'twochildren' => [ 'nullable' ],
            'twochildren_id' => [ 'nullable' ],


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
    public function getGroupId(){
        if ($this->has('groupSet')){
            return @$this->get('groupSet')['id'];
        }
        return null;
    }
    public function getDiscountId(){
        if ($this->has('discount')){
            return @$this->get('discount')['id'];
        }
        return null;
    }
    public function getChildrenId(){
        if ($this->has('twochildren')){
            return @$this->get('twochildren')['id'];
        }
        return 0;
    }
    public function getPhone($PhoneSave='phoneSave'){
        if ($this->has($PhoneSave)){
            return @$this->get($PhoneSave)['formattedNumber'];
        }
        return '';
    }
    public function getPhoneCountry($PhoneSave='phoneSave'){
        if ($this->has($PhoneSave)){
            return @$this->get($PhoneSave)['countryCode'];
        }
        return '';
    }
}
