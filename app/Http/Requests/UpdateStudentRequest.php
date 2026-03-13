<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // $this->route('id') — ID из URL для исключения текущей записи из unique-проверки
        $studentId = $this->route('id');

        return [
            'name'            => 'nullable|string|max:100',
            'surname'         => 'nullable|string|max:100',
            'email'           => 'nullable|email|max:150|unique:recruting_student,email,' . $studentId,
            'phone'           => 'nullable|string|max:20',
            'subject'         => 'nullable|string|max:100',
            'status'          => 'nullable|string|in:new,registered,verified,signed,paid,expelled,transferred,archived',
            'group_id'        => 'nullable|integer',
            'teacher_id'      => 'nullable|integer',
            'parent_name'     => 'nullable|string|max:100',
            'parent_surname'  => 'nullable|string|max:100',
            'parent_phone'    => 'nullable|string|max:20',
            'parent_passport' => 'nullable|string|max:50',
            'city'            => 'nullable|string|max:100',
            'country'         => 'nullable|string|max:100',
            'address'         => 'nullable|string|max:200',
            'apartment'       => 'nullable|string|max:20',
            'zip'             => 'nullable|string|max:20',
            'dob'             => 'nullable|date',
            'reg_comment'     => 'nullable|string|max:1000',
            'photo_consent'   => 'nullable|boolean',
        ];
    }
}
