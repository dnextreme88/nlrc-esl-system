<?php

namespace App\Livewire;

use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReservationCalendar extends Component
{
    public $current_month_and_year;
    public $current_date;
    public $last_date;
    public $max_date;
    public array $meeting_slots = [];
    public array $time_slots = [];
    public array $dates = [];
    public bool $is_today = true;
    public bool $is_max_date = false;

    #[On('saving-reservation-slots')]
    public function reserve_slots($reserved_slots)
    {
        $teacher_id = Auth::user()->id;
        $current_dates = array_column($reserved_slots, 'date');
        $start_times = array_column($reserved_slots, 'start_time');
        $end_times = array_column($reserved_slots, 'end_time');

        // Initially update slots to 0
        MeetingSlot::where('teacher_id', $teacher_id)
            ->whereBetween('meeting_date', [$this->current_date->format('Y-m-d'), $this->last_date->format('Y-m-d')])
            ->update(['is_reserved' => 0]);

        // Then only update the selected ones
        foreach ($reserved_slots as $index => $value) {
            MeetingSlot::updateOrCreate(
                [
                    'teacher_id' => $teacher_id,
                    'meeting_date' => $current_dates[$index],
                    'start_time' => $start_times[$index],
                    'end_time' => $end_times[$index],
                ],
                ['is_reserved' => 1]
            );
        }

        $this->dispatch('updated-reserved-slots');
    }

    public function render_prev_seven_days()
    {
        $this->current_date = $this->current_date->copy()->subDays(7);
        $this->last_date = $this->last_date->copy()->subDays(7);

        $this->dispatch('rendered-calendar');
    }

    public function render_next_seven_days()
    {
        $this->current_date = $this->last_date->copy()->addDays(1);
        $this->last_date = $this->current_date->copy()->addDays(6);

        $this->dispatch('rendered-calendar');
    }

    public function check_date_limits(): void
    {
        $this->is_today = $this->current_date == Carbon::today();
        $this->is_max_date = $this->last_date == Carbon::today()->addDays(27);
    }

    public function mount()
    {
        $this->current_date = Carbon::today();
        $this->last_date = $this->current_date->copy()->addDays(6);
        $this->max_date = $this->current_date->copy()->addDays(27);
        $this->time_slots = Helpers::populate_time_slots();
    }

    #[On('rendered-calendar')]
    #[On('updated-reserved-slots')]
    public function render()
    {
        $this->current_month_and_year = $this->current_date->format('F Y');

        $this->meeting_slots = MeetingSlot::where('teacher_id', Auth::user()->id)
            ->whereBetween('meeting_date', [$this->current_date->format('Y-m-d'), $this->last_date->format('Y-m-d')])
            ->with('meeting_slot_users')
            ->get()
            ->toArray();

        $this->dates = [];
        // Build the array for the current day as well as the next 6 days
        $period = CarbonPeriod::create($this->current_date, '1 day', $this->last_date);

        foreach ($period as $date) {
            $this->dates[$date->format('D')] = [
                'date' => $date->format('Y-m-d'),
                'date_shorthand' => $date->format('m/d'),
            ];
        }

        $this->check_date_limits();

        return view('livewire.reservation-calendar');
    }
}
