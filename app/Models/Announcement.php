<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'tags',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function (self $announcement) {
            $announcement->slug = Str::slug($announcement->title, '-');
        });

        static::updating(function (self $announcement) {
            $announcement->slug = Str::slug($announcement->title, '-');
        });
    }
}
