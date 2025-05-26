<?php

namespace App\Models\Meetings;

use App\Models\Meetings\Meeting;
use App\Models\User;
use App\Traits\DateTrait;
use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingUser extends Model
{
    use DateTrait;
    use IdTrait;

    protected $fillable = [
        'meeting_id',
        'student_id',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
