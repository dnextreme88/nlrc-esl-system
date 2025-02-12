<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'is_reserved',
    ];

    public function meeting_slot_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_slot_users', 'meeting_slot_id', 'student_id')
            ->withTimestamps();
    }
}
