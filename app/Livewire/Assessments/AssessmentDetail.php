<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentsChoice;
use App\Models\AssessmentsQuestion;
use App\Models\AssessmentsStudents;
use App\Models\AssessmentsStudentsAnswer;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class AssessmentDetail extends Component
{
    public $current_assessment;
    public $current_assessment_questions;
    public $is_assessment_passed = false;
    public array $student_answers = [];
    public $score_percentage = '0';
    public $correct_answers_count = 0;
    public $correct_answers_of_assessment_count = 0;
    public $unit_id;
    public $user;

    #[On('validating-answers')]
    public function validate_answers($student_answers)
    {
        $student_answers = array_filter($student_answers);

        $this->correct_answers_of_assessment_count = 0;
        $this->correct_answers_count = 0;

        // Save assessment data and student answers to DB
        $student_assessment = AssessmentsStudents::create([
            'assessment_id' => $this->current_assessment->id,
            'student_id' => $this->user->id,
        ]);

        AssessmentsQuestion::where('assessment_id', $this->current_assessment->id)->get()
            ->map(function ($question, $index) use ($student_assessment, $student_answers) {
                $data = array_map(function ($choice_name) use ($student_assessment, $question) {
                    $choice = AssessmentsChoice::where('assessment_question_id', $question->id)->where('choice', $choice_name)
                        ->first();

                    return [
                        'assessment_student_id' => $student_assessment->id,
                        'assessment_question_id' => $question->id,
                        'assessment_choice_id' => $choice->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }, $student_answers[($index + 1)]);

                AssessmentsStudentsAnswer::insert($data);
            });

        $student_score = AssessmentsStudentsAnswer::get_student_score($student_answers, $this->current_assessment_questions);
        $this->correct_answers_count = $student_score['correct_answers_count'];
        $this->correct_answers_of_assessment_count = $student_score['correct_answers_of_assessment_count'];
        $this->score_percentage = $student_score['score_percentage'];

        Toaster::success('Congratulations!' .($this->score_percentage == 100.00 ? ' You completed this assessment!' : ' You answered all questions of this assessment!'));
        $this->dispatch('showed-assessment-results');
    }

    public function mount($id, $slug)
    {
        $this->unit_id = request('unit_id'); // Get from query param

        $this->current_assessment = Assessment::where('id', $id)->where('slug', $slug)
            ->first();
        $this->current_assessment_questions = AssessmentsQuestion::get_assessment_questions_and_choices($id, $slug);

        $latest_attempt = AssessmentsStudents::select(['id', 'created_at'])->studentAssessment($this->current_assessment->id, Auth::user()->id)
            ->latest()
            ->first();

        if ($latest_attempt) {
            $student_answers = AssessmentsStudentsAnswer::get_student_answers($latest_attempt->id);
            $student_score = AssessmentsStudentsAnswer::get_student_score($student_answers, $this->current_assessment_questions);

            if ($student_score['score_percentage'] == 100.00) {
                $this->is_assessment_passed = true;
                $this->student_answers = $student_answers;
                $this->correct_answers_count = $student_score['correct_answers_count'];
                $this->correct_answers_of_assessment_count = $student_score['correct_answers_of_assessment_count'];
                $this->score_percentage = $student_score['score_percentage'];
            }
        }
    }

    public function render()
    {
        $this->user = User::findOrFail(Auth::user()->id);
        $unit = Unit::find($this->unit_id);

        if ($this->current_assessment) {
            if ($this->user->cannot('view', [$this->current_assessment, $unit])) {
                abort(403);
            } else {
                return view('livewire.assessments.assessment-detail');
            }
        } else {
            abort(404);
        }
    }
}
