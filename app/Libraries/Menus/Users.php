<?php

namespace App\Libraries\Menus;

use App\Interfaces\MenuInterface;

class Users implements MenuInterface
{
    public function getActions(): ?array
    {
        return [
            'view',
            'create',
            'delete'
        ];
    }

    public function getOrder(): int
    {
        return 1;
    }
}
