<?php

namespace App\Livewire\Meetings;

use App\Enums\MeetingStatuses;
use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TeacherAvailabilitySlots extends Component
{
    public array $possible_dates = [];
    public array $time_slots = [];
    public $meeting_slots = [];
    public $meeting_date;
    public int $count_pending_reserved_slots = 0;
    public bool $is_meeting_date_chosen = false;
    public $show_update_slots_confirmation_modal = false;
    public $time_in_user_timezone_tomorrow;

    public function reserve_slot_modal()
    {
        $this->show_update_slots_confirmation_modal = true;
    }

    #[On('updating-slots')]
    public function update_slots($slots_to_update)
    {
        foreach ($slots_to_update as $slot) {
            $spliced_start_time = explode(' ', $slot['start_time']);
            $spliced_end_time = explode(' ', $slot['end_time']);
            $start_date = $this->meeting_date. ' ' .$spliced_start_time[0];

            MeetingSlot::updateOrCreate(
                [
                    'teacher_id' => Auth::user()->id,
                    'meeting_date' => Carbon::parse($start_date, Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d'),
                    'start_time' => Carbon::parse($start_date, Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d H:i:s'),
                    'end_time' => Carbon::parse($this->meeting_date. ' ' .$spliced_end_time[0], Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d H:i:s'),
                ],
                ['is_opened' => $slot['is_opened']]
            );
        }

        // Reset
        $this->show_update_slots_confirmation_modal = false;
        $this->meeting_date = null;
        $this->is_meeting_date_chosen = false;

        Toaster::success('You have successfully updated your availabilities!');
    }

    public function show_available_times_for_selected_date()
    {
        $this->validate(['meeting_date' => ['required', 'date', 'date_format:Y-m-d']]);

        $this->time_slots = Helpers::populate_time_slots('H:i A'); // Not necessary, it's just to ensure that 12:00 AM goes first before 1:00 AM

        $meeting_date = $this->meeting_date;

        $this->meeting_slots = MeetingSlot::isTeacherId(Auth::user()->id)
            ->where('status', MeetingStatuses::PENDING->value)
            ->with('meeting_slots_users')
            ->get()
            ->filter(function ($val) use ($meeting_date) {
                return Helpers::parse_time_to_user_timezone($val['start_time'])->format('Y-m-d') == $meeting_date;
            })
            ->map(function ($slot) {
                $slot->start_time = Helpers::parse_time_to_user_timezone($slot->start_time)->format('H:i A');
                $slot->end_time = Helpers::parse_time_to_user_timezone($slot->end_time)->format('H:i A');
                $slot->route = route('meetings.detail', ['meeting_uuid' => $slot->meeting_uuid]);

                return $slot;
            });

        $this->count_pending_reserved_slots = count($this->meeting_slots->filter(
            fn ($val) => $val['is_opened'] == 1 && $val['meeting_slots_users']->isEmpty()
        ));

        $this->is_meeting_date_chosen = true;

        $this->dispatch('rendered-time-slots', meeting_slots: $this->meeting_slots->values()->toArray(), time_slots: $this->time_slots);
    }

    public function mount()
    {
        $time_in_user_timezone = Carbon::now()->toUserTimezone();
        $this->time_in_user_timezone_tomorrow = $time_in_user_timezone->copy()->tomorrow();
        $next_28_days = $time_in_user_timezone->copy()->addDays(28);

        // Get the dates starting tomorrow
        $this->possible_dates = CarbonPeriod::create($this->time_in_user_timezone_tomorrow, '1 day', $next_28_days)->toArray();
    }

    #[On('rendered-time-slots')]
    #[On('updated-reserved-slots')]
    public function render()
    {
        return view('livewire.meetings.teacher-availability-slots');
    }
}
