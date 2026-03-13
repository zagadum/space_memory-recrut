<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:recruting_student,email',
            'password' => 'required|min:6',

            // Личные данные — необязательны при первичной регистрации
            'name'             => 'nullable|string|max:100',
            'surname'          => 'nullable|string|max:100',
            'lastname'         => 'nullable|string|max:100',
            'parent_name'      => 'nullable|string|max:100',
            'parent_surname'   => 'nullable|string|max:100',
            'parent_phone'     => 'nullable|string|max:20',
            'parent_passport'  => 'nullable|string|max:50',
            'dob'              => 'nullable|date',
            'country'          => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:200',
            'zip'              => 'nullable|string|max:20',
            'apartment'        => 'nullable|string|max:20',

            // Согласия
            'photo_consent'    => 'nullable|boolean',
            'terms_accepted'   => 'nullable|boolean',
            'privacy_accepted' => 'nullable|boolean',
            'reg_comment'      => 'nullable|string|max:1000',
        ];
    }
}
