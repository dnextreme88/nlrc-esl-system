<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AssessmentList extends Component
{
    use WithPagination;

    public function render()
    {
        $assessments = Assessment::whereHas('students', fn ($query) => $query->where('student_id', Auth::user()->id))
            ->orderBy('title')
            ->paginate(5);

        return view('livewire.assessments.assessment-list', compact('assessments'));
    }
}
