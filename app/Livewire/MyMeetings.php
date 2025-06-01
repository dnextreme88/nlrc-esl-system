<?php

namespace App\Livewire;

use App\Helpers\Helpers;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class MyMeetings extends Component
{
    public $current_month;
    public $current_year;
    public bool $is_student_role;
    public bool $is_teacher_role;

    public function render_prev_month()
    {
        $this->current_month = $this->current_month->copy()->subMonths(1);
        $this->current_year = $this->current_month->format('Y');

        $this->dispatch('rendered-calendar');
    }

    public function render_next_month()
    {
        $this->current_month = $this->current_month->copy()->addMonths(1);
        $this->current_year = $this->current_month->format('Y');

        $this->dispatch('rendered-calendar');
    }

    public function mount()
    {
        $current_date = Carbon::today();
        $this->current_month = $current_date;
        $this->current_year = $this->current_month->format('Y');

        $this->is_student_role = Helpers::is_student_role();
        $this->is_teacher_role = Helpers::is_teacher_role();
    }

    #[On('rendered-calendar')]
    public function render()
    {
        return view('livewire.my-meetings');
    }
}
