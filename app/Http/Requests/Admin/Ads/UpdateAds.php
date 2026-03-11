<?php

namespace App\Http\Requests\Admin\Ads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAds extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;// Gate::allows('admin.ads.edit', $this->city);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
           // 'id' => ['sometimes', 'string'],
            'url' => ['sometimes', 'string'],
            'photo' => ['sometimes'],
            //'img' => ['sometimes', 'string'],
            'enabled' => ['sometimes' ],
            'enabledOK' => ['sometimes'],

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



        return $sanitized;
    }
}
