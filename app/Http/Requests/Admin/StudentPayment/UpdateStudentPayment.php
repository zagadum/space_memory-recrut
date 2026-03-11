<?php

namespace App\Http\Requests\Admin\StudentPayment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateStudentPayment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
       // return Gate::allows('admin.student-payment.edit', $this->studentPayment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'integer'],
            'date_pay' => ['nullable', 'date'],
            'date_finish' => ['nullable', 'date'],
            'sum_aboniment' => ['sometimes', 'integer'],
            'type_aboniment' => ['sometimes', 'string'],
            'type_pay' => ['sometimes', 'string'],
            'enabled' => ['sometimes', 'boolean'],

        ];
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();


        //Add your code for manipulation with request data here

        return $sanitized;
    }
}
