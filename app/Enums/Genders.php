<?php

namespace App\Enums;

enum Genders: string
{
    case FEMALE = 'female';
    case MALE = 'male';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FEMALE => 'Female',
            self::MALE => 'Male',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FEMALE => 'danger',
            self::MALE => 'info',
        };
    }
}
