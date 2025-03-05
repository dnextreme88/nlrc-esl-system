<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Models\MeetingSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SelectMeetingSlot extends Component
{
    public array $possible_dates = [];
    public $available_meeting_slots_time = [];
    public $meeting_date;
    public bool $is_meeting_date_chosen = false;
    public $show_reserve_slot_confirmation_modal;
    public $start_time;
    public $end_time;
    public $time_in_user_timezone_tomorrow;
    public int $max_students_per_slot = 5;

    public function reserve_slot_modal($start_time, $end_time)
    {
        $this->show_reserve_slot_confirmation_modal = true;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public function reserve_slot()
    {
        // Assign to a random meeting slot if there are multiple teachers who has the same meeting date and times
        $random_meeting_slot = MeetingSlot::select(['id', 'start_time', 'end_time'])->where('meeting_date', $this->meeting_date)
            ->where('start_time', $this->start_time)
            ->where('end_time', $this->end_time)
            ->inRandomOrder()
            ->first();

        $random_meeting_slot->meeting_slot_users()->attach($random_meeting_slot->id, [
            'student_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->show_reserve_slot_confirmation_modal = false;
        $this->meeting_date = null;
        $this->is_meeting_date_chosen = false;

        $this->dispatch('reserved-slot');
    }

    public function show_available_times_for_selected_date()
    {
        $this->validate(['meeting_date' => ['required', 'date', 'date_format:Y-m-d']]);

        $meeting_date = $this->meeting_date;

        // TODO: TO REFACTOR LOGIC TO LIMIT 5 STUDENTS PER MEETING SLOT PER UNIQUE TEACHER
        $this->available_meeting_slots_time = [];
        $this->available_meeting_slots_time = MeetingSlot::where('status', MeetingStatuses::PENDING->value)->where('is_opened', 1)
            ->orderBy('start_time', 'ASC')
            ->get()
            ->filter(function ($val) use ($meeting_date) {
                return Carbon::parse($val['start_time'])->toUserTimezone()->format('Y-m-d') == $meeting_date;
            });

        $this->is_meeting_date_chosen = true;
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
    }

    #[On('reserved-slot')]
    public function render()
    {
        return view('livewire.select-meeting-slot');
    }
}
