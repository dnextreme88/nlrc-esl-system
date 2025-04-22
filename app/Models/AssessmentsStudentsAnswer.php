<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentsStudentsAnswer extends Model
{
    protected $fillable = [
        'assessment_choice_id',
        'assessment_question_id',
        'student_id',
    ];

    public function choice(): BelongsTo
    {
        return $this->belongsTo(AssessmentsChoice::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentsQuestion::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
