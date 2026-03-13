<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpelledStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'     => 'nullable|in:new,expelled,archived',
            'teacher_id' => 'nullable|integer',
            'group_id'   => 'nullable|integer',
            'phone'      => 'nullable|string|max:20',
        ];
    }
}
