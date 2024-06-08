<?php

namespace App\Enums;

enum Store: string
{
    case HADIMKOY = 'HADIMKOY MAĞAZA';
    case ETICARET = 'ETİCARET MAĞAZA';
    case MAIN = 'MERKEZ DEPO';

    public static function mainStores(): array
    {
        return [
            self::HADIMKOY->value,
            self::ETICARET->value,
        ];
    }
}
