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
            'name'     => 'required|string|max:255',
            'surname'  => 'required|string|max:255',
            'dob'      => 'required|date',

            // Родитель (маппится в parent1_*)
            'parent_name'    => 'required|string|max:255',
            'parent_surname' => 'required|string|max:255',
            'parent_phone'   => 'required|string|max:50',
            'parent_passport' => 'required|string|max:50',

            // Адрес
            'country'   => 'required|string|max:100',
            'city'      => 'required|string|max:255',
            'address'   => 'required|string|max:255',
            'zip'       => 'required|string|max:20',
            'apartment' => 'nullable|string|max:50',

            // Согласия
            'photo_consent'       => 'nullable|boolean',
            'terms_accepted'      => 'accepted',
            'privacy_accepted'    => 'accepted',
            'data_processing'     => 'accepted',
            'urgent_start'        => 'accepted',
            'recording_consent'   => 'nullable|boolean',
            'marketing_consent'   => 'nullable|boolean',
            'hobbies'             => 'nullable|string',

            // Опционально
            'reg_comment' => 'nullable|string|max:1000',
            'language'    => 'nullable|string|max:5',
        ];
    }
}
