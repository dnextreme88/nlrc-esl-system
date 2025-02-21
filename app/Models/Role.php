<?php

namespace App\Models;

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
}
