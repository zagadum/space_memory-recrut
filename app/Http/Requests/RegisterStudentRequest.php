<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed for registration:', $validator->errors()->toArray());
        
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Ошибка валидации',
            'errors'  => $validator->errors()
        ], 422));
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:recruting_student,email',
            'password' => 'required|min:6',

            // Личные данные теперь обязательны
            'name'             => 'required|string|max:100',
            'surname'          => 'required|string|max:100',
            'lastname'         => 'nullable|string|max:100',
            'parent_name'      => 'required|string|max:100',
            'parent_surname'   => 'required|string|max:100',
            'parent_phone'     => 'required|string|max:20',
            'parent_passport'  => 'required|string|max:50',
            'dob'              => 'required|date',
            'country'          => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'address'          => 'required|string|max:200',
            'zip'              => 'required|string|max:20',
            'apartment'        => 'nullable|string|max:20',

            // Согласия
            'photo_consent'     => 'nullable|boolean',
            'terms_accepted'    => 'required|accepted',
            'privacy_accepted'  => 'required|accepted',
            'data_processing'   => 'required|accepted',
            'urgent_start'      => 'required|accepted',
            'recording_consent' => 'nullable|boolean',
            'marketing_consent' => 'nullable|boolean',
            'reg_comment'       => 'nullable|string|max:1000',
            'locale'            => 'nullable|string|max:10',
        ];
    }
}
