<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveNewsMainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_uk' => 'string|required',
            'name' => 'string|sometimes',
            'html_uk' => 'string|required',
            'html' => 'sometimes',
            'html_en' => 'sometimes',
            'name_en' => 'string|sometimes',
            'friendly_url' => 'string|required',
            'status' => 'string|required',
            'date_add' => 'string',
            'img_src' => 'nullable|mimes:jpeg,jpg,png,gif|max:100000'
        ];
    }
}
