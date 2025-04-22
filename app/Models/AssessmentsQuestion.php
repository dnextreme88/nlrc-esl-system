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
}
