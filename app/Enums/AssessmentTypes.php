<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssessmentTypes: string implements HasColor, HasLabel
{
    case EXERCISE = 'exercise';
    case MOCK_ASSESSMENT = 'mock-assessment';
    case WARM_UP = 'warm-up';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EXERCISE => 'Exercise',
            self::MOCK_ASSESSMENT => 'Mock Assessment',
            self::WARM_UP => 'Warm-up Activity',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EXERCISE => 'success',
            self::MOCK_ASSESSMENT => 'gray',
            self::WARM_UP => 'warning',
        };
    }
}
