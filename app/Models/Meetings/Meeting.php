<?php

namespace App\Models\Meetings;

use App\Models\User;
use App\Traits\DateTrait;
use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

class Meeting extends Model
{
    use DateTrait;
    use HasFactory;
    use IdTrait;

    protected $fillable = [
        'teacher_id',
        'meeting_uuid',
        'meeting_date',
        'start_time',
        'end_time',
        'meeting_link',
        'notes',
        'status',
        'is_opened',
    ];

    public function meeting_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_users', 'meeting_id', 'student_id')
            ->withTimestamps();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStudentsCountAttribute()
    {
        return $this->meeting_users()->count();
    }

    public static function booted(): void
    {
        static::creating(function (self $meeting) {
            $meeting->meeting_uuid = Uuid::uuid4()->toString();
        });
    }
}
