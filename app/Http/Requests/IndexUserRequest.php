<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
        ];
    }

    public function attributes(): array
    {
        return [
            'search' => __('search'),
        ];
    }
}
