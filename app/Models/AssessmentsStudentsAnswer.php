<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentsStudentsAnswer extends Model
{
    protected $fillable = [
        'assessment_student_id',
        'assessment_question_id',
        'assessment_choice_id',
    ];

    public function assessment_student(): BelongsTo
    {
        return $this->belongsTo(AssessmentsStudents::class);
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(AssessmentsChoice::class, 'assessment_choice_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentsQuestion::class);
    }

    // Custom model function
    protected function get_student_score($assessment_answers, $assessment_questions): array
    {
        $correct_answers_count = 0;
        $correct_answers_of_assessment_count = 0;

        foreach ($assessment_questions as $question_num => $assessment_question) {
            $student_answers_for_question = collect($assessment_answers[($question_num + 1)]);

            $correct_answers_for_question = AssessmentsChoice::select(['id', 'choice', 'is_correct'])->where('assessment_question_id', $assessment_question['id'])
                ->get()
                ->filter(fn ($choice) => $choice->is_correct == 1)
                ->pluck('choice');

            $correct_answers_of_assessment_count += $correct_answers_for_question->count();

            // Compare student answers with correct answers
            $correct_answers_count += $student_answers_for_question->filter(function ($answer) use ($correct_answers_for_question) {
                return $correct_answers_for_question->contains($answer);
            })
                ->count();
        }

        // Compute final score
        $score_percentage = $correct_answers_count > 0 ? round((($correct_answers_count / $correct_answers_of_assessment_count) * 100), 2) : 0;

        return [
            'correct_answers_count' => $correct_answers_count,
            'correct_answers_of_assessment_count' => $correct_answers_of_assessment_count,
            'score_percentage' => $score_percentage,
        ];
    }
}
