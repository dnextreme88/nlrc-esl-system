<?php

namespace App\Helpers;

use Carbon\Carbon;

class Helpers
{
    public static function populate_time_slots($format = 'H:i'): array
    {
        $time_slots = [];

        // Fill the time slots
        $twelve_o_clock_am = Carbon::createFromTime(0, 0, 0);
        $eleven_thirty_pm = Carbon::createFromTime(23, 30, 0);

        while ($twelve_o_clock_am->lte($eleven_thirty_pm)) {
            $next_time = $twelve_o_clock_am->copy()->addMinutes(30);
            $time_slots[] = [
                'start_time' => $twelve_o_clock_am->format($format),
                'end_time' => $next_time->format($format)
            ];
            $twelve_o_clock_am->addMinutes(30);
        }

        return $time_slots;
    }
}
