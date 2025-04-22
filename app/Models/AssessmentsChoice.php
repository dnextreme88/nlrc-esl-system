<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentsChoice extends Model
{
    protected $fillable = [
        'assessment_question_id',
        'choice',
        'is_correct',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentsQuestion::class);
    }
}
