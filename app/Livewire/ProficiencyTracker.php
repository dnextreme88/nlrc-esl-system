<?php

namespace App\Livewire;

use App\Models\ProficienciesUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProficiencyTracker extends Component
{
    public $student_proficiencies;

    public function render()
    {
        $this->student_proficiencies = ProficienciesUser::isStudentId(Auth::user()->id)
            ->get();

        return view('livewire.proficiency-tracker');
    }
}
