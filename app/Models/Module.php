<?php

namespace App\Models;

use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;
    use IdTrait;

    protected $fillable = [
        'proficiency_id',
        'name',
        'slug',
        'description',
    ];

    public function module_students(): HasMany
    {
        return $this->hasMany(ModulesStudent::class);
    }

    public function module_teachers(): HasMany
    {
        return $this->hasMany(ModulesTeacher::class);
    }

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function studentsCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->module_students()->count(),
        );
    }

    public function teachersCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->module_teachers()->count(),
        );
    }

    public function unitsCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->units()->count(),
        );
    }

    protected static function booted()
    {
        static::creating(function (self $module) {
            $module->slug = Str::slug($module->name, '-');
        });

        static::updating(function (self $module) {
            $module->slug = Str::slug($module->name, '-');
        });
    }
}
