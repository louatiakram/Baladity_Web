<?php

namespace App\Enum;

class EtatTache
{
    const TODO = 'TODO';
    const DOING = 'DOING';
    const DONE = 'DONE';

    public static function toArray(): array
    {
        return [
            self::TODO => 'TODO',
            self::DOING => 'DOING',
            self::DONE => 'DONE',
        ];
    }
}