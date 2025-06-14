<?php

namespace App\Models;

use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class AssessmentsStudents extends Model
{
    use IdTrait;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'assessment_uuid',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function scopeStudentAssessment($query, int $assessment_id, int $student_id)
    {
        return $query->where('assessment_id', $assessment_id)
            ->isStudentId($student_id);
    }

    public static function booted(): void
    {
        static::creating(function (self $assessments_students) {
            $assessments_students->assessment_uuid = Uuid::uuid4()->toString();
        });
    }
}
