<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePagesRequest extends FormRequest
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
            'name_uk' => 'string|sometimes',
            'name' => 'string|sometimes',
            'html_uk' => 'string|sometimes',
            'html' => 'string|nullable',
            'html_en' => 'string|nullable',
            'name_en' => 'string|sometimes',
            'alias' => 'string|required',
            'status' => 'string|sometimes',
            'type'=>'required|string|in:pages,content',

        ];
    }
}
