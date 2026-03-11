<?php

namespace App\Http\Requests\Admin\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

class StoreShopProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
       // return Gate::allows('admin.franchisee.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'status'     => ['required', 'integer', 'in:0,1,2'],
            'description' => ['required', 'string'],
            'count' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'size_width' => ['required', 'numeric'],
            'size_length' => ['required', 'numeric'],
            'size_height' => ['required', 'numeric'],
            'deliveryclass' => ['required', 'integer', 'in:0,1'],
            'description' => ['required', 'string'],
            'image1' => ['nullable', 'string'],
            'image2' => ['nullable', 'string'],
            'image3' => ['nullable', 'string'],
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
