<?php

namespace App\Models\Meetings;

use App\Helpers\Helpers;
use App\Models\Meetings\MeetingUpdate;
use App\Models\User;
use App\Traits\DateTrait;
use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function meeting_updates(): HasMany
    {
        return $this->hasMany(MeetingUpdate::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function duration(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => Helpers::parse_time_to_user_timezone($this->start_time)->format('h:i A'). ' ~ ' .Helpers::parse_time_to_user_timezone($this->end_time)->format('h:i A')
        );
    }

    public function studentsCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->meeting_users()->count(),
        );
    }

    public static function booted(): void
    {
        static::creating(function (self $meeting) {
            $meeting->meeting_uuid = Uuid::uuid4()->toString();
        });
    }
}
