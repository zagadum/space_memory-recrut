<?php

namespace App\Http\Requests\Admin\Franchisee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexFranchisee extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
        //return Gate::allows('admin.franchisee.index');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'in:id,surname,first_name,patronymic,country_id,region_id,city_id,phone,email,fin_royalty,fin_pr,fin_legal,fin_address,fin_regno,fin_price_aboniment,fin_currency,passport,iin,subscibe_email,language,enabled|nullable',
            'orderDirection' => 'in:asc,desc|nullable',
            'search' => 'string|nullable',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',

        ];
    }
}
