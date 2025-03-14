<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulesTeacher extends Model
{
    protected $fillable = [
        'module_id',
        'teacher_id',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
