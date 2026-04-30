<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportServiceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => __('service_types.import_file'),
        ];
    }
}
