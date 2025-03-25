<?php

namespace App\Models;

use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulesStudent extends Model
{
    use IdTrait;

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
