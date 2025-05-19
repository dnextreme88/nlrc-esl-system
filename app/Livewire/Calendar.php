<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class Calendar extends Component
{
    public $current_month;
    public $current_year;
    public $days_in_month;
    public $first_day_of_month;
    public $today;
    public $calendar_start;
    public $calendar_end;
    public $show_previous;
    public $show_next;
    public $prev_days;
    public $current_month_days;
    public $dates;
    public $single_dates;

    public function calculate_days()
    {
        $this->calendar_start = Carbon::create($this->current_year, $this->current_month, 1);
        $this->calendar_end = (clone $this->calendar_start)->addMonth();

        $this->show_previous = $this->calendar_start->gt($this->today->copy()->startOfMonth());
        $this->show_next = $this->calendar_end->lte($this->today->copy()->addMonth()->startOfMonth());

        $this->days_in_month = $this->calendar_start->daysInMonth;
        $this->first_day_of_month = $this->calendar_start->dayOfWeek;
        $previous_month_days = (clone $this->calendar_start)->subMonth()->daysInMonth;

        // Build trailing days from the previous month
        $this->prev_days = collect();
        for ($i = $this->first_day_of_month - 1; $i >= 0; $i--) {
            $this->prev_days->push($previous_month_days - $i);
        }

        // Current month's days
        $this->current_month_days = collect();
        for ($day = 1; $day <= $this->days_in_month; $day++) {
            $current_date = Carbon::create($this->current_year, $this->current_month, $day)->startOfDay()->toUserTimezone();
            $is_today = $current_date->eq($this->today);
            $date_in_range = $this->single_dates->contains(function ($d) use ($current_date) {
                return $d == Carbon::parse($current_date)->format('Y-m-d');
            });

            $parsed_date = Carbon::parse($current_date)->format('Y-m-d');
            $has_slots = array_key_exists($parsed_date, $this->dates) && $this->dates[$parsed_date]->isNotEmpty();

            $day_data = [
                'day' => $day,
                'has_slots' => $has_slots,
                'parsed_date' => $parsed_date,
                'date_in_range' => $date_in_range,
            ];

            if ($is_today) {
                $day_data['is_today'] = $is_today;
            }

            $this->current_month_days->push($day_data);
        }
    }

    public function show_times(string $meeting_date = '')
    {
        $this->dispatch('show-times-for-date', meeting_date: $meeting_date);
    }

    public function previous_month()
    {
        $this->current_month--;
        if ($this->current_month < 1) {
            $this->current_month = 12;
            $this->current_year--;
        }

        $this->calculate_days();
    }

    public function next_month()
    {
        $this->current_month++;
        if ($this->current_month > 12) {
            $this->current_month = 1;
            $this->current_year++;
        }

        $this->calculate_days();
    }

    public function mount($dates = [])
    {
        $this->dates = $dates; // Contains id, is_student_in_slot, start_time, and end_time
        $this->single_dates = collect(array_keys($dates));

        $this->today = Carbon::now()->startOfDay();
        $this->current_month = $this->today->month;
        $this->current_year = $this->today->year;

        $this->calculate_days();
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
