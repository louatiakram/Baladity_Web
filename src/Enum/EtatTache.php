<?php

namespace App\Enum;

class EtatTache
{
    const TO_DO = 'TO_DO';
    const DOING = 'DOING';
    const DONE = 'DONE';

    public static function toArray(): array
    {
            return [
                self::TO_DO => 'TO_DO',
                self::DOING => 'DOING',
                self::DONE => 'DONE',
            ];
        }
}