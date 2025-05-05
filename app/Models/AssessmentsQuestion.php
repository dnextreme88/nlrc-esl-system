<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentsQuestion extends Model
{
    protected $fillable = [
        'assessment_id',
        'question',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(AssessmentsChoice::class, 'assessment_question_id');
    }

    public function choicesCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->choices()->count(),
        );
    }

    public function choicesAnswersCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->choices()->where('is_correct', true)->count(),
        );
    }

    // Custom model function
    protected function get_assessment_questions_and_choices($assessment_id, $slug): array
    {
        $assessment_questions = Assessment::where('id', $assessment_id)->where('slug', $slug)
            ->first()
            ->questions;

        foreach ($assessment_questions as $index => $question) {
            $choices = $question->choices;

            $assessment_questions[$index]['choices'] = $choices->toArray();
            $assessment_questions[$index]['no_of_correct_answers'] = $choices->filter(fn ($choice): bool => $choice->is_correct == true)->count();
        }

        return $assessment_questions->toArray();
    }
}
