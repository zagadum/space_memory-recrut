<?php

declare(strict_types=1);

namespace App\Http\Requests\Recruiting;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'email_subject'  => 'required|string|max:255',
            'email_template' => 'required|string',
            'file'           => 'required|file|mimes:csv,txt',
        ];
    }
}
