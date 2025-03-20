<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingSlotsUser extends Model
{
    use DateTrait;

    protected $fillable = [
        'meeting_slot_id',
        'student_id',
    ];

    public function meeting_slot(): BelongsTo
    {
        return $this->belongsTo(MeetingSlot::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
