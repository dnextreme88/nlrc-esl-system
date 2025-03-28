<?php

namespace App\Models;

use App\Traits\IdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;
    use IdTrait;

    protected $fillable = [
        'module_id',
        'name',
        'slug',
        'description',
    ];

    public function unit_attachments(): HasMany
    {
        return $this->hasMany(UnitsAttachment::class);
    }

    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }
}
