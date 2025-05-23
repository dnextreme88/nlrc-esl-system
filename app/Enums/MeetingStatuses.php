<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingStatuses: string implements HasColor, HasLabel
{
    case CANCELLED = 'Cancelled';
    case COMPLETED = 'Completed';
    case NO_SHOW = 'No Show';
    case PENDING = 'Pending';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::NO_SHOW => 'No Show',
            self::PENDING => 'Pending',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CANCELLED => 'danger',
            self::COMPLETED => 'success',
            self::NO_SHOW => 'gray',
            self::PENDING => 'warning',
        };
    }
}
