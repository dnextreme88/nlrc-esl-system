<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    protected $fillable = [
        'level_code',
        'name',
        'description',
    ];
}
