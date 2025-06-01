<?php

namespace App\Helpers;

use App\Enums\Roles;
use App\Models\Announcement;
use App\Models\Meetings\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Helpers
{
    public static function get_notifications($notifications): array
    {
        $parsed_notifications = [];

        foreach ($notifications as $notification) {
            if ($notification['type'] == 'announcement-sent') {
                $announcement = Announcement::find($notification->data['announcement_id']);

                array_push($parsed_notifications, array_merge(
                    $notification->toArray(),
                    [
                        'announcement' => [
                            'id' => $announcement['id'],
                            'title' => $announcement['title'],
                            'slug' => $announcement['slug'],
                            'description' => $announcement['description'],
                        ]
                    ]
                ));
            } else if ($notification['type'] == 'meeting-booked') {
                $meeting = Meeting::find($notification->data['meeting_id']);

                array_push($parsed_notifications, array_merge(
                    $notification->toArray(),
                    [
                        'meeting' => [
                            'id' => $meeting['id'],
                            'meeting_uuid' => $meeting['meeting_uuid'],
                            'start_time' => $meeting['start_time'],
                            'end_time' => $meeting['end_time'],
                        ]
                    ]
                ));
            }
        }

        return $parsed_notifications;
    }

    public static function is_admin_role(): bool
    {
        return Auth::user()->role->name == Roles::ADMIN->value;
    }

    public static function is_student_role(): bool
    {
        return Auth::user()->role->name == Roles::STUDENT->value;
    }

    public static function is_teacher_role(): bool
    {
        return in_array(Auth::user()->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value]);
    }

    public static function parse_time_to_user_timezone($time): Carbon
    {
        return Carbon::parse($time)->toUserTimezone();
    }

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
