<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MeetingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'meeting_date',
        'start_time',
        'end_time',
        'notes',
        'status',
        'is_opened',
    ];

    public function getStudentsCountAttribute()
    {
        return $this->meeting_slot_users()->count();
    }

    public function meeting_slot_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_slot_users', 'meeting_slot_id', 'student_id')
            ->withTimestamps();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
