<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $leadId = $this->route('id');

        return [
            'name'       => 'nullable|string|max:255',
            'surname'    => 'nullable|string|max:255',
            'lastname'   => 'nullable|string|max:255',
            'email'      => 'nullable|email|unique:recruting_student,email,' . $leadId,
            'phone'      => 'nullable|string|max:20',
            'subject'    => 'nullable|string|max:100',
            'status'     => 'nullable|in:new,registered,verified,signed,paid,expelled,transferred,archived',
            'group_id'   => 'nullable|integer',
            'teacher_id' => 'nullable|integer',
            'enabled'    => 'nullable|boolean',
        ];
    }
}
