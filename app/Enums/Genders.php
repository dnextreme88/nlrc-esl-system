<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Genders: string implements HasColor, HasLabel
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
