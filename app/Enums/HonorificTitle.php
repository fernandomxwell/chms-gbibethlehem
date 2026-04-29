<?php

namespace App\Enums;

enum HonorificTitle: string
{
    case Bpk = 'bpk';
    case Ibu = 'ibu';
    case Sdr = 'sdr';
    case Sdri = 'sdri';

    public function label(): string
    {
        return __('honorific.' . $this->value);
    }
}
