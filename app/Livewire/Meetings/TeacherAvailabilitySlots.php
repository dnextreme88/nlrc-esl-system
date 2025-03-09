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

class TeacherAvailabilitySlots extends Component
{
    public array $possible_dates = [];
    public array $time_slots = [];
    public $meeting_slots;
    public $meeting_date;
    public int $count_pending_reserved_slots = 0;
    public bool $is_meeting_date_chosen = false;
    public $time_in_user_timezone_tomorrow;

    #[On('saving-reservation-slots')]
    public function reserve_slots($reserved_slots)
    {
        $start_times = array_column($reserved_slots, 'start_time');
        $end_times = array_column($reserved_slots, 'end_time');

        foreach ($reserved_slots as $index => $value) {
            MeetingSlot::updateOrCreate(
                [
                    'teacher_id' => Auth::user()->id,
                    'meeting_date' => Carbon::parse($start_times[$index], Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d'),
                    'start_time' => Carbon::parse($start_times[$index], Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d H:i:s'),
                    'end_time' => Carbon::parse($end_times[$index], Auth::user()->timezone)->setTimezone('UTC')->format('Y-m-d H:i:s'),
                ],
                ['is_opened' => $value['is_opened']]
            );
        }

        // Reset
        $this->meeting_date = '';
        $this->is_meeting_date_chosen = false;

        $this->dispatch('updated-reserved-slots');
    }

    public function show_available_times_for_selected_date()
    {
        $this->validate(['meeting_date' => ['required', 'date', 'date_format:Y-m-d']]);

        $meeting_date = $this->meeting_date;

        $this->meeting_slots = MeetingSlot::where('teacher_id', Auth::user()->id)
            ->where('status', MeetingStatuses::PENDING->value)
            ->with('meeting_slot_users')
            ->get()
            ->filter(function ($val) use ($meeting_date) {
                return Carbon::parse($val['start_time'])->toUserTimezone()->format('Y-m-d') == $meeting_date;
            });

        $this->count_pending_reserved_slots = count($this->meeting_slots->filter(fn ($val) =>
            $val['is_opened'] == 1 && $val['meeting_slot_users']->isEmpty()
        ));

        $this->is_meeting_date_chosen = true;

        $this->dispatch('rendered-time-slots');
    }

    public function mount()
    {
        $time_in_user_timezone = Carbon::now()->toUserTimezone();
        $this->time_in_user_timezone_tomorrow = $time_in_user_timezone->copy()->tomorrow();
        $next_28_days = $time_in_user_timezone->copy()->addDays(28);

        // Get the dates starting tomorrow
        $period = CarbonPeriod::create($this->time_in_user_timezone_tomorrow, '1 day', $next_28_days);

        foreach ($period as $date) {
            $this->possible_dates[] = $date;
        }

        $this->time_slots = Helpers::populate_time_slots('H:i:s'); // Not necessary, it's just to ensure that 12:00 AM goes first before 1:00 AM
    }

    #[On('rendered-time-slots')]
    #[On('updated-reserved-slots')]
    public function render()
    {
        return view('livewire.meetings.teacher-availability-slots');
    }
}
