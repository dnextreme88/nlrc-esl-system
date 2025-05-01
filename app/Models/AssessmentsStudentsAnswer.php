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
        return $this->belongsTo(AssessmentsChoice::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentsQuestion::class);
    }
}
