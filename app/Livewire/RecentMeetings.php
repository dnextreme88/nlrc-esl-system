<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentMeetings extends Component
{
    public $meetings;

    public function render()
    {
        $user = Auth::user();

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $this->meetings = MeetingSlot::select(['id', 'meeting_uuid', 'meeting_date', 'start_time', 'end_time', 'status'])->where('teacher_id', $user->id)
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->whereHas('meeting_slots_users')
                ->getMeetingDates('past')
                ->orderMeetings('DESC')
                ->limit(5)
                ->get();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $this->meetings = MeetingSlotsUser::select(['ms.id', 'ms.meeting_uuid', 'ms.meeting_date', 'ms.start_time', 'ms.end_time', 'ms.status'])->join('meeting_slots AS ms', 'meeting_slots_users.meeting_slot_id', 'ms.id')
                ->where('student_id', $user->id)
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->getMeetingDates('past')
                ->orderMeetings('DESC')
                ->limit(5)
                ->get();
        }

        return view('livewire.recent-meetings');
    }
}
