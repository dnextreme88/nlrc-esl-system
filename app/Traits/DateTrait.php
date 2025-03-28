<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateTrait
{
    // Prefixing functions with scope allow you to chain query constraints,
    // which is useful for code readability and the DRY principle
    // Sample usage: $meetings_asc_order = MeetingSlot::getMeetingDates('past')->get();
    protected function scopeGetMeetingDates($query, $tense)
    {
        $operator = '';

        if ($tense == 'past') {
            $operator = '<';
        } else if ($tense == 'future') {
            $operator = '>';
        } else if ($tense == 'today') {
            $operator = '=';
        }

        return $query->where('meeting_date', $operator, Carbon::today()->format('Y-m-d'));
    }

    protected function scopeOrderMeetings($query, $order = 'ASC')
    {
        return $query->orderBy('meeting_date', $order)
            ->orderBy('start_time', $order);
    }
}
