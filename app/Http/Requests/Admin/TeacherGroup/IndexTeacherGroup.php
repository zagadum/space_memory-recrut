<?php

namespace App\Http\Requests\Admin\TeacherGroup;

use AllowDynamicProperties;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

#[AllowDynamicProperties]
class IndexTeacherGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
        //return Gate::allows('admin.teacher-group.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:id,franchisee_id,teacher_id,group_id,name,age_id,address,locations,start_day,workday,enabled|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
