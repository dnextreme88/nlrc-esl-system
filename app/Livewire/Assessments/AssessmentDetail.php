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

        foreach ($this->current_assessment_questions as $question_num => $assessment_question) {
            $student_answers_for_question = collect($student_answers[($question_num + 1)]);

            $correct_answers_for_question = AssessmentsChoice::select(['id', 'choice', 'is_correct'])->where('assessment_question_id', $assessment_question['id'])
                ->get()
                ->filter(fn ($choice) => $choice->is_correct == 1)
                ->pluck('choice');
            $this->correct_answers_of_assessment_count += $correct_answers_for_question->count();

            // Compare student answers with correct answers
            $this->correct_answers_count += $student_answers_for_question->filter(function ($answer) use ($correct_answers_for_question) {
                return $correct_answers_for_question->contains($answer);
            })
                ->count();
        }

        // Compute final score
        $this->score_percentage = $this->correct_answers_count > 0 ? round((($this->correct_answers_count / $this->correct_answers_of_assessment_count) * 100), 2) : 0;

        Toaster::success('Congratulations!' .($this->score_percentage == 100.00 ? ' You completed this assessment!' : ' You answered all questions of this assessment!'));
        $this->dispatch('showed-assessment-results');
    }

    public function mount($id, $slug)
    {
        $this->current_assessment = Assessment::where('id', $id)->where('slug', $slug)
            ->first();

        $this->unit_id = request('unit_id'); // Get from query param

        $assessment_questions = $this->current_assessment->questions;
        foreach ($assessment_questions as $index => $question) {
            $choices = $question->choices;

            $assessment_questions[$index]['choices'] = $choices->toArray();
            $assessment_questions[$index]['no_of_correct_answers'] = $choices->filter(fn ($choice): bool => $choice->is_correct == true)->count();
        }

        $this->current_assessment_questions = $assessment_questions->toArray();
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
