<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentsQuestion;
use App\Models\AssessmentsStudents;
use App\Models\AssessmentsStudentsAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AttemptHistory extends Component
{
    public $student_assessments;
    public $student_assessment_questions;

    public function mount($assessment_id)
    {
        $assessment = Assessment::find($assessment_id);

        $this->student_assessment_questions = AssessmentsQuestion::get_assessment_questions_and_choices($assessment_id, $assessment->slug);
        $this->student_assessments = AssessmentsStudents::select(['id', 'created_at'])->where('assessment_id', $assessment_id)
            ->where('student_id', Auth::user()->id)
            ->latest()
            ->get();

        foreach ($this->student_assessments as $index => $student_assessment) {
            $student_answers = AssessmentsStudentsAnswer::where('assessment_student_id', $student_assessment->id)
                ->with('choice') // Eager load the related choice
                ->get()
                ->groupBy('assessment_question_id') // Group by question ID
                ->map(fn ($group) =>
                    $group->map(fn ($answer) => $answer->choice->choice)->values()->all() // Array of choices for each question
                )
                ->toArray();

            $student_score = AssessmentsStudentsAnswer::get_student_score($student_answers, $this->student_assessment_questions);

            $this->student_assessments[$index]['score'] = $student_score['score_percentage'];
            $this->student_assessments[$index]['status'] = $student_score['score_percentage'] == 100.00 ? 'Passed' : 'Failed';
        }
    }

    public function render()
    {
        return view('livewire.assessments.attempt-history');
    }
}
