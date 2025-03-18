<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitsAttachment extends Model
{
    protected $fillable = [
        'unit_id',
        'file_name',
        'file_type',
        'file_path',
        'description',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
