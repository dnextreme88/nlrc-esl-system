<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProficienciesUser extends Model
{
    protected $fillable = [
        'proficiency_id',
        'user_id',
    ];

    protected $table = 'proficiencies_users';

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
