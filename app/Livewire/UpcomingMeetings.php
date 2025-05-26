<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class UpcomingMeetings extends Component
{
    public $meetings = [];
    public $meeting_id;
    public bool $show_cancel_meeting_modal = false;
    public $cancel_reason = '';
    public bool $show_reschedule_meeting_modal = false;
    public $reschedule_new_date;
    public $reschedule_new_start_time;
    public $reschedule_reason = '';
    public $start_times = [];

    public function cancel_meeting_modal($meeting_id)
    {
        $this->show_cancel_meeting_modal = true;
        $this->meeting_id = $meeting_id;
    }

    public function cancel_meeting()
    {
        $this->validate([
            'meeting_id' => ['required', 'exists:meetings,id'],
            'cancel_reason' => ['required', 'max:255'],
        ]);

        Meeting::where('id', $this->meeting_id)
            ->update([
                'notes' => $this->cancel_reason,
                'status' => MeetingStatuses::CANCELLED->value,
            ]);

        $this->reset();
        $this->show_cancel_meeting_modal = false;

        Toaster::success('You have successfully cancelled your meeting!');
        $this->dispatch('cancelled-meeting');
    }

    public function reschedule_meeting_modal($meeting_id)
    {
        $this->show_reschedule_meeting_modal = true;
        $this->meeting_id = $meeting_id;
    }

    public function reschedule_meeting()
    {
        $this->validate([
            'meeting_id' => ['required', 'exists:meetings,id'],
            'reschedule_new_date' => ['required', 'date', 'date_format:Y-m-d', 'after:today', 'after:1900-01-01'],
            'reschedule_new_start_time' => ['required'],
            'reschedule_reason' => ['required', 'max:255'],
        ]);

        Meeting::where('id', $this->meeting_id)
            ->update([
                'meeting_date' => $this->reschedule_new_date,
                'start_time' => $this->reschedule_new_start_time,
                'end_time' => Carbon::parse($this->reschedule_new_start_time)->addMinutes(30)->format('g:i A'),
                'notes' => $this->reschedule_reason,
                'status' => MeetingStatuses::PENDING->value,
            ]);

        $this->reset();
        $this->show_reschedule_meeting_modal = false;

        Toaster::success('You have successfully rescheduled your meeting!');
        $this->dispatch('rescheduled-meeting');
    }

    #[On('cancelled-meeting')]
    #[On('rescheduled-meeting')]
    public function render()
    {
        $this->start_times = Helpers::populate_time_slots();

        $user = Auth::user();

        if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
            $this->meetings = Meeting::select(['id', 'meeting_uuid', 'meeting_date', 'start_time', 'end_time', 'status'])->isTeacherId($user->id)
                ->whereIn('status', [MeetingStatuses::CANCELLED->value, MeetingStatuses::PENDING->value])
                ->whereHas('meeting_users')
                ->getMeetingDates('future')
                ->orderMeetings('ASC')
                ->limit(5)
                ->get();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $this->meetings = MeetingUser::select(['ms.id', 'ms.meeting_uuid', 'ms.meeting_date', 'ms.start_time', 'ms.end_time', 'ms.status'])->join('meetings AS ms', 'meeting_users.meeting_id', 'ms.id')
                ->isStudentId($user->id)
                ->whereIn('status', [MeetingStatuses::CANCELLED->value, MeetingStatuses::PENDING->value])
                ->getMeetingDates('future')
                ->orderMeetings('ASC')
                ->limit(5)
                ->get();
        }

        return view('livewire.upcoming-meetings');
    }
}
