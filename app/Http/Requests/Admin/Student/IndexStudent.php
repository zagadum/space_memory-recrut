<?php

namespace App\Http\Requests\Admin\Student;

use AllowDynamicProperties;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

#[AllowDynamicProperties]
class IndexStudent extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
       // return Gate::allows('admin.student.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:id,group_id,teacher_id,email,subcribe_email,surname,lastname,patronymic,dob,phone,start_day,date_finish,sum_aboniment,diams,disount,balance,language,blocking_reason,email_verified_at,blocking_date,enabled|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
