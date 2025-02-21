<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotUser;
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
            ->doesntHave('meeting_slot_users')
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

        // TODO: TO REFACTOR LOGIC TO LIMIT 5 STUDENTS PER MEETING SLOT
        // RIGHT NOW, THE MEETING SLOT DOES NOT SHOW UP IF THEY HAVE A RECORD IN
        // meeting_slot_users TABLE
        $initial_time_slots = [];
        $this->available_meeting_slots_time = [];
        $initial_time_slots = MeetingSlot::select(['meeting_date', 'start_time', 'end_time'])->where('meeting_date', $this->meeting_date)
            ->where('status', MeetingStatuses::PENDING->value)
            // NOTE: The only purpose of the is_reserved field is to check if the slot is "turned on" on the teachers' side.
            // If it is turned on, students can book a meeting on that particular date and time.
            // However if it is 0 on the DB, the teacher toggled the slot after it was created on the DB and thus that slot
            // is closed and cannot be booked by students
            ->where('is_reserved', 1)
            ->doesntHave('meeting_slot_users')
            ->get()
            ->toArray();

        // Logic below is to check for existing times already reserved by the student for this particular date
        // So they don't book the same time on the same date twice
        $meetings_of_student = MeetingSlotUser::where('student_id', Auth::user()->id)->with('meeting_slot')
            ->get();

        $time_slots = Helpers::populate_time_slots(); // Not necessary, it's just to ensure that 12:00 AM goes first before 1:00 AM
        $this->available_meeting_slots_time = array_values(
            array_uintersect($time_slots, $initial_time_slots, fn ($time_slot, $available_time_for_date) => strcmp($time_slot['start_time'], $available_time_for_date['start_time']))
        );

        if ($meetings_of_student) {
            foreach ($meetings_of_student as $meeting) {
                // Check if the start and end times of a particular date exists
                $has_booked_time_on_meeting_date = array_filter($initial_time_slots, function ($meeting_slot) use ($meeting) {
                    return $meeting['meeting_slot']['meeting_date'] == $meeting_slot['meeting_date'] &&
                        $meeting['meeting_slot']['start_time'] == $meeting_slot['start_time'] &&
                        $meeting['meeting_slot']['end_time'] == $meeting_slot['end_time'];
                });

                // If it does, remove those so the student doesn't have to book the same time on the same date twice
                if ($has_booked_time_on_meeting_date) {
                    $this->available_meeting_slots_time = array_filter($this->available_meeting_slots_time, function ($time_slot) use ($meeting) {
                        return $meeting['meeting_slot']['start_time'] != $time_slot['start_time'] &&
                            $meeting['meeting_slot']['end_time'] != $time_slot['end_time'];
                    });
                }
            }
        }

        $this->available_meeting_slots_time = array_values($this->available_meeting_slots_time); // Final time slots to be shown
        $this->is_meeting_date_chosen = true;
    }

    public function mount()
    {
        $next_28_days = Carbon::today()->addDays(28);
        $period = CarbonPeriod::create(Carbon::today(), '1 day', $next_28_days);

        foreach ($period as $date) {
            $this->possible_dates[] = [
                'db_format' => $date->format('Y-m-d'),
                'view_format' => $date->format('F d, Y'),
            ];
        }
    }

    #[On('reserved-slot')]
    public function render()
    {
        return view('livewire.select-meeting-slot');
    }
}
