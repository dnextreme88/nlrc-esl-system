<?php

namespace App\Models\Meetings;

use App\Models\Meetings\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingUpdate extends Model
{
    const UPDATED_AT = null; // Tell Laravel to ignore updated_at

    protected $fillable = [
        'meeting_id',
        'user_id',
        'description',
        'old_meeting_time',
        'new_meeting_time',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
