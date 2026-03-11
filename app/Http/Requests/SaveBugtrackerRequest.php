<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBugtrackerRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'bugdescription' => ['required', 'string'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    /*
public function rules()
{
    return [
        'bugdescription' => 'required|min:10',
        'bugsteps' => 'required|min:10',
        'file_count' => 'required|gte:1',
    ];
}


public function messages()
{
    return [
        // Messages for `bugdescription`
        'bugdescription.required' => 'Bug description cannot be empty.',
        'bugdescription.min' => 'Bug description must be at least :min characters long.',

        // Messages for `bugsteps`
        'bugsteps.required' => 'Reproduction steps cannot be empty.',
        'bugsteps.min' => 'Reproduction steps must be at least :min characters long.',

        // Messages for `file_count`
        'file_count.required' => 'At least one file must be uploaded.',
        'file_count.gte' => 'At least one file must be uploaded. (file_count cannot be 0)',
    ];
}
*/

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
