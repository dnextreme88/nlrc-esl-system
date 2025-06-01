<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function role(): HasOne
    {
        return $this->hasOne(User::class);
    }

    protected function scopeIsAdmin($query)
    {
        return $query->where('name', Roles::ADMIN->value);
    }

    protected function scopeIsStudent($query)
    {
        return $query->where('name', Roles::STUDENT->value);
    }

    protected function scopeIsTeacher($query)
    {
        return $query->where('name', Roles::HEAD_TEACHER->value)
            ->orWhere('name', Roles::TEACHER->value);
    }
}
