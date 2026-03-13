<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'      => 'required|email|unique:recruting_student,email',
            'name'       => 'nullable|string|max:255',
            'surname'    => 'nullable|string|max:255',
            'lastname'   => 'nullable|string|max:255',
            'group_id'   => 'nullable|integer',
            'teacher_id' => 'nullable|integer',
            'manager'    => 'nullable|string|max:100',
        ];
    }
}
