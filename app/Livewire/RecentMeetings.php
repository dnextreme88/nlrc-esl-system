<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentMeetings extends Component
{
    public $meetings;

    public function render()
    {
        $user = Auth::user();

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $this->meetings = MeetingSlot::select(['id', 'meeting_date', 'start_time', 'end_time', 'status'])->where('teacher_id', $user->id)
                ->where('meeting_date', '<', Carbon::today()->format('Y-m-d'))
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->whereHas('meeting_slot_users')
                ->orderBy('meeting_date', 'DESC')
                ->orderBy('start_time', 'DESC')
                ->get();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $this->meetings = MeetingSlotUser::select(['ms.id', 'ms.meeting_date', 'ms.start_time', 'ms.end_time', 'ms.status'])->join('meeting_slots AS ms', 'meeting_slot_users.meeting_slot_id', 'ms.id')
                ->where('student_id', $user->id)
                ->where('ms.meeting_date', '<', Carbon::today()->format('Y-m-d'))
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->orderBy('ms.meeting_date', 'DESC')
                ->orderBy('start_time', 'DESC')
                ->get();
        }

        return view('livewire.recent-meetings');
    }
}
