<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class UpcomingMeetings extends Component
{
    public $meetings = [];
    public $meeting_slot_id;
    public bool $show_cancel_meeting_modal = false;
    public $cancel_reason = '';
    public bool $show_reschedule_meeting_modal = false;
    public $reschedule_new_date;
    public $reschedule_new_start_time;
    public $reschedule_reason = '';
    public $start_times = [];

    public function cancel_meeting_modal($meeting_slot_id)
    {
        $this->show_cancel_meeting_modal = true;
        $this->meeting_slot_id = $meeting_slot_id;
    }

    public function cancel_meeting()
    {
        $this->validate([
            'meeting_slot_id' => ['required', 'exists:meeting_slots,id'],
            'cancel_reason' => ['required', 'max:255'],
        ]);

        MeetingSlot::where('id', $this->meeting_slot_id)
            ->update([
                'notes' => $this->cancel_reason,
                'status' => MeetingStatuses::CANCELLED->value,
            ]);

        $this->reset();
        $this->show_cancel_meeting_modal = false;

        Toaster::success('You have successfully cancelled your meeting!');
        $this->dispatch('cancelled-meeting');
    }

    public function reschedule_meeting_modal($meeting_slot_id)
    {
        $this->show_reschedule_meeting_modal = true;
        $this->meeting_slot_id = $meeting_slot_id;
    }

    public function reschedule_meeting()
    {
        $this->validate([
            'meeting_slot_id' => ['required', 'exists:meeting_slots,id'],
            'reschedule_new_date' => ['required', 'date', 'date_format:Y-m-d', 'after:today', 'after:1900-01-01'],
            'reschedule_new_start_time' => ['required'],
            'reschedule_reason' => ['required', 'max:255'],
        ]);

        MeetingSlot::where('id', $this->meeting_slot_id)
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
            $this->meetings = MeetingSlot::select(['id', 'meeting_uuid', 'meeting_date', 'start_time', 'end_time', 'status'])->where('teacher_id', $user->id)
                ->whereIn('status', [MeetingStatuses::CANCELLED->value, MeetingStatuses::PENDING->value])
                ->whereHas('meeting_slots_users')
                ->getMeetingDates('future')
                ->orderMeetings('ASC')
                ->limit(5)
                ->get();
        } else if ($user->role->name == Roles::STUDENT->value) {
            $this->meetings = MeetingSlotsUser::select(['ms.id', 'ms.meeting_uuid', 'ms.meeting_date', 'ms.start_time', 'ms.end_time', 'ms.status'])->join('meeting_slots AS ms', 'meeting_slots_users.meeting_slot_id', 'ms.id')
                ->where('student_id', $user->id)
                ->whereIn('status', [MeetingStatuses::CANCELLED->value, MeetingStatuses::PENDING->value])
                ->getMeetingDates('future')
                ->orderMeetings('ASC')
                ->limit(5)
                ->get();
        }

        return view('livewire.upcoming-meetings');
    }
}
