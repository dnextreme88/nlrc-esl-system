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
        $this->student_assessments = AssessmentsStudents::select(['id', 'assessment_uuid', 'created_at'])->studentAssessment($assessment_id, Auth::user()->id)
            ->latest()
            ->get()
            ->map(function ($student_assessment) {
                $student_answers = AssessmentsStudentsAnswer::get_student_answers($student_assessment->id);
                $student_score = AssessmentsStudentsAnswer::get_student_score($student_answers, $this->student_assessment_questions);

                $student_assessment->score = $student_score['score_percentage'];
                $student_assessment->status = $student_score['score_percentage'] == 100.00 ? 'Passed' : 'Failed';

                return $student_assessment;
            });
    }

    public function render()
    {
        return view('livewire.assessments.attempt-history');
    }
}
