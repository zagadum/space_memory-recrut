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
            // Обязательные
            'email'    => 'required|email|unique:recruting_student,email',
            'password' => 'required|min:6',

            // Ребёнок
            'name'     => 'nullable|string|max:255',
            'surname'  => 'nullable|string|max:255',
            'dob'      => 'nullable|date',

            // Родитель (маппится в parent1_*)
            'parent_name'    => 'nullable|string|max:255',
            'parent_surname' => 'nullable|string|max:255',
            'parent_phone'   => 'nullable|string|max:50',

            // Адрес
            'country'   => 'nullable|string|max:100',
            'city'      => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'zip'       => 'nullable|string|max:20',
            'apartment' => 'nullable|string|max:50',

            // Согласия
            'photo_consent'       => 'nullable|boolean',
            'terms_accepted'      => 'required|boolean',
            'privacy_accepted'    => 'required|boolean',
            'data_processing'     => 'required|boolean',
            'urgent_start'        => 'nullable|boolean',
            'recording_consent'   => 'nullable|boolean',
            'marketing_consent'   => 'nullable|boolean',

            // Опционально
            'reg_comment' => 'nullable|string|max:1000',
            'language'    => 'nullable|string|max:5',
        ];
    }
}
