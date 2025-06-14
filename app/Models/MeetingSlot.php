<?php

namespace App\Models;

use App\Traits\DateTrait;
use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

class MeetingSlot extends Model
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

    public function meeting_slots_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_slots_users', 'meeting_slot_id', 'student_id')
            ->withTimestamps();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStudentsCountAttribute()
    {
        return $this->meeting_slots_users()->count();
    }

    public static function booted(): void
    {
        static::creating(function (self $meeting_slot) {
            $meeting_slot->meeting_uuid = Uuid::uuid4()->toString();
        });
    }
}
