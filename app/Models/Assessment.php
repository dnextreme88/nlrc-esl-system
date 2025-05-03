<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'is_active',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentsQuestion::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(AssessmentsStudents::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_assessments', 'assessment_id', 'unit_id');
    }

    public function questionsCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->questions()->count(),
        );
    }

    protected static function booted()
    {
        static::creating(function (self $assessment) {
            $assessment->slug = Str::slug($assessment->title, '-');
        });

        static::updating(function (self $assessment) {
            $assessment->slug = Str::slug($assessment->title, '-');
        });
    }
}
