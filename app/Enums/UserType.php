<?php

namespace App\Enums;

enum UserType: string
{
    case PESSOA = 'pessoa';
    case EMPRESA = 'empresa';

    public function label(): string
    {
        return match ($this) {
            self::PESSOA => 'Pessoa física',
            self::EMPRESA => 'Empresa',
        };
    }
}
