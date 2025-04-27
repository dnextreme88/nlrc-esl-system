<?php

namespace App\Models;

use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory;
    use IdTrait;

    protected $fillable = [
        'module_id',
        'name',
        'slug',
        'description',
    ];

    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }

    public function unit_assessments(): HasMany
    {
        return $this->hasMany(UnitsAssessment::class);
    }

    public function unit_attachments(): HasMany
    {
        return $this->hasMany(UnitsAttachment::class);
    }

    protected static function booted()
    {
        static::creating(function (self $unit) {
            $unit->slug = Str::slug($unit->name, '-');
        });

        static::updating(function (self $unit) {
            $unit->slug = Str::slug($unit->name, '-');
        });
    }
}
