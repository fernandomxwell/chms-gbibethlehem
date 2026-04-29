<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\HonorificTitle;
use App\Enums\Status;
use App\Traits\ValidatesHonorificGender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreCongregantRequest extends FormRequest
{
    use ValidatesHonorificGender;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'honorific_title' => ['nullable', new Enum(HonorificTitle::class)],
            'full_name' => 'required|string|max:100',
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth' => ['nullable', 'date', Rule::date()->todayOrBefore()],
            'phone_number' => ['nullable', 'regex:/^(?:\+62|62|0)8[1-9][0-9]{6,9}$/'],
            'email' => 'nullable|email|max:100',
            'date_of_baptism' => ['nullable', 'date', Rule::date()->todayOrBefore(), 'after_or_equal:date_of_birth'],
            'status' => ['required', new Enum(Status::class)],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'phone_number.regex' => __('validation.phone_number_regex'),
        ];
    }

    /**
     * Customize the validation attribute names that apply to the request.
     */
    public function attributes()
    {
        return [
            'honorific_title' => __('honorific_title'),
            'full_name' => __('full_name'),
            'gender' => __('gender'),
            'date_of_birth' => __('date_of_birth'),
            'phone_number' => __('phone_number'),
            'email' => __('email'),
            'date_of_baptism' => __('date_of_baptism'),
            'status' => __('status'),
        ];
    }
}
