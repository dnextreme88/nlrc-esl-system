<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressionLevel extends Model
{
    protected $fillable = [
        'level',
        'name',
        'description',
    ];
}
