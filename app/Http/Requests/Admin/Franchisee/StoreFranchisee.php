<?php

namespace App\Http\Requests\Admin\Franchisee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

class StoreFranchisee extends FormRequest
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

        $validationFactory->extend('regionRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть область');
        $validationFactory->extend('countryRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Виберіть країну');
        $validationFactory->extend('cityRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['id']) && !empty($value['id'])){$ret=true;}return $ret; }, 'Поле Місто є обов\'язковим для заповнення.');
        $validationFactory->extend('currencyRule', function ($attribute, $value, $parameters) {$ret=false;if (isset($value['code']) && !empty($value['code'])){$ret=true;}return $ret; }, 'Поле Валюта є обов\'язковим для заповнення.');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [

            'region' => ['required','regionRule'],
            'country' => ['required','countryRule'],
            'city' => ['required','cityRule'],
            'currency' => ['required','currencyRule'],

            //'phoneNumber' => ['required'],

            'surname' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'patronymic' => ['nullable', 'string'],
            'country_id' => ['sometimes'],//???
            'region_id' => ['sometimes'],//???
            'city_id' => ['sometimes'],//???
            'locality' => ['sometimes'],
            'phoneSave' => ['required', 'array'],

            'email' => ['required', 'email', 'string'],
            'password' => ['required',  'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],//  'confirmed',
            'fin_royalty' => ['required', 'integer'],
            'fin_pr' => ['required', 'integer'],
            'fin_legal' => ['required', 'string'],
            'fin_address' => ['required', 'string'],
            'fin_vid' => ['required', 'string'],
            'fin_regno' => ['sometimes', 'string'],
            'fin_price_aboniment' => ['required', 'integer'],


            'passport' => ['sometimes'],
            'iin' => ['sometimes'],
            'subscibe_email' => ['nullable','sometimes', 'email','string'],
            'language' => ['required'],
            'enabled' => ['sometimes', 'boolean'],
            'formatInternational'=>['sometimes'],
            'countryCallingCode'=>['sometimes']


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
    public function getCurrencyCode(){
        if ($this->has('currency')){
            return @$this->get('currency')['code'];
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
