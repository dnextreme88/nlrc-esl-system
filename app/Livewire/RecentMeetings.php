<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentMeetings extends Component
{
    public $meetings;

    public function render()
    {
        $user = Auth::user();

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $this->meetings = Meeting::select(['id', 'meeting_uuid', 'meeting_date', 'start_time', 'end_time', 'status'])->isTeacherId($user->id)
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->whereHas('meeting_users')
                ->getMeetingDates('past')
                ->orderMeetings('DESC')
                ->limit(5)
                ->get();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $this->meetings = MeetingUser::select(['ms.id', 'ms.meeting_uuid', 'ms.meeting_date', 'ms.start_time', 'ms.end_time', 'ms.status'])->join('meetings AS ms', 'meeting_users.meeting_id', 'ms.id')
                ->isStudentId($user->id)
                ->whereNot('status', MeetingStatuses::PENDING->value)
                ->getMeetingDates('past')
                ->orderMeetings('DESC')
                ->limit(5)
                ->get();
        }

        return view('livewire.recent-meetings');
    }
}
