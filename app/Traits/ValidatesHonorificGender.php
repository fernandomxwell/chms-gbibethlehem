<?php

namespace App\Traits;

use Illuminate\Validation\Validator;

trait ValidatesHonorificGender
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            static::checkHonorificGender(
                $this->input('honorific_title'),
                $this->input('gender'),
                $validator,
            );
        });
    }

    public static function checkHonorificGender(?string $honorific, ?string $gender, Validator $validator): void
    {
        if (! $honorific || ! $gender) {
            return;
        }

        $maleHonorifics = ['bpk', 'sdr'];
        $femaleHonorifics = ['ibu', 'sdri'];

        if (in_array($honorific, $maleHonorifics) && $gender !== 'male') {
            $validator->errors()->add('honorific_title', __('validation.honorific_gender_mismatch'));
        } elseif (in_array($honorific, $femaleHonorifics) && $gender !== 'female') {
            $validator->errors()->add('honorific_title', __('validation.honorific_gender_mismatch'));
        }
    }
}
