<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnitsAssessment extends Model
{
    protected $fillable = [
        'unit_id',
        'assessment_id',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function unit(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }
}
