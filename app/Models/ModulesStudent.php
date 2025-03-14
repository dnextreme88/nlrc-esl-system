<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulesStudent extends Model
{
    protected $fillable = [
        'module_id',
        'student_id',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
