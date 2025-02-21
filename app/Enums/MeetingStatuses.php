<?php

namespace App\Enums;

enum MeetingStatuses: string
{
    case CANCELLED = 'Cancelled';
    case COMPLETED = 'Completed';
    case NO_SHOW = 'No Show';
    case PENDING = 'Pending';
}
