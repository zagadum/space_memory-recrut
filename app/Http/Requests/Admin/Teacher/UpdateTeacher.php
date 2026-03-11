<?php

namespace App\Http\Requests\Admin\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

class UpdateTeacher extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
       //return Gate::allows('admin.teacher.edit', $this->teacher);
    }
    public function __construct(ValidationFactory $validationFactory) {

        $validationFactory->extend('franchiseeRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть Франшиза');

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
            'surname' => ['sometimes', 'string'],
            'first_name' => ['sometimes', 'string'],
            'patronymic' => ['nullable', 'string'],
            'phone' => ['sometimes', 'string'],
            'dob' => ['sometimes', 'date'],
            'email' => ['sometimes', 'email', 'string'],
            'password' => ['nullable',  'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],//  'confirmed',
            'passport' => ['nullable', 'string'],
            'iin' => ['nullable', 'string'],
            'subscibe_email' => ['nullable', 'string'],
            'language' => ['sometimes', 'string'],
            'fin_cabinet' => ['sometimes', 'boolean'],
            'enabled' => ['sometimes', 'boolean'],

        ];
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getModifiedData(): array
    {
        $data = $this->only(collect($this->rules())->keys()->all());
        if (!Config::get('admin-auth.activation_enabled')) {
            $data['activated'] = true;
        }
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $data;
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
