<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentsQuestion;
use App\Models\AssessmentsStudents;
use App\Models\AssessmentsStudentsAnswer;
use Livewire\Component;

class AssessmentResult extends Component
{
    public $correct_answers_count;
    public $correct_answers_of_assessment_count;
    public $current_assessment;
    public $current_assessment_questions;
    public $score_percentage;
    public $student_answers;

    public function mount($assessment_uuid)
    {
        $this->current_assessment = AssessmentsStudents::where('assessment_uuid', $assessment_uuid)->first();

        $assessment = Assessment::find($this->current_assessment->assessment_id);

        $this->current_assessment_questions = AssessmentsQuestion::get_assessment_questions_and_choices($assessment->id, $assessment->slug);
        $this->student_answers = AssessmentsStudentsAnswer::get_student_answers($this->current_assessment->id);
        $student_score = AssessmentsStudentsAnswer::get_student_score($this->student_answers, $this->current_assessment_questions);

        $this->correct_answers_count = $student_score['correct_answers_count'];
        $this->correct_answers_of_assessment_count = $student_score['correct_answers_of_assessment_count'];
        $this->score_percentage = $student_score['score_percentage'];
    }

    public function render()
    {
        return view('livewire.assessments.assessment-result');
    }
}
