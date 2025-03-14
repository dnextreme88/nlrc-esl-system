<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'proficiency_id',
        'name',
        'slug',
        'description',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function module_students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'module_students', 'module_id', 'student_id')
            ->withTimestamps();
    }

    public function module_teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'module_teachers', 'module_id', 'teacher_id')
            ->withTimestamps();
    }
}
