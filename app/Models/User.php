<?php

namespace App\Models;

use App\Enums\Roles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'date_of_birth',
        'is_active',
        'gender',
        'timezone',
        'notification_settings',
        'password',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'notification_settings' => 'array',
            'password' => 'hashed',
        ];
    }

    public function meeting_slots(): HasMany
    {
        return $this->hasMany(MeetingSlotsUser::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ModulesStudent::class);
    }

    public function proficiencies_users(): BelongsToMany
    {
        return $this->belongsToMany(Proficiency::class, 'proficiencies_users', 'proficiency_id', 'student_id')
            ->withTimestamps();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role->name == Roles::ADMIN->value;
    }

    public function getFilamentName(): string
    {
        return $this->last_name. ', ' .$this->first_name. ' ' .$this->middle_name;
    }

    public function profilePhotoUrl(): Attribute
    {
        $name_initials = Str::upper(trim($this->first_name[0]). trim($this->last_name[0]));

        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => $this->profile_photo_path ? asset('storage/images/' .$this->profile_photo_path) : 'https://ui-avatars.com/api/?name=' .urlencode($name_initials). '&color=FFFFFF&background=40A36A'
        );
    }

    public function updateProfilePhoto(?UploadedFile $photo)
    {
        $config_disk = config('jetstream.profile_photo_disk', 'profile_photos');

        // Delete old file
        if (!is_null($this->attributes['profile_photo_path'])) {
            Storage::disk($config_disk)->delete($this->attributes['profile_photo_path']);
        }

        if ($photo) {
            $file_path = $photo->storePublicly('profile-photos', $config_disk);

            $this->attributes['profile_photo_path'] = $file_path;
        } else if (!$photo && $this->attributes['profile_photo_path']) {
            $this->attributes['profile_photo_path'] = null;
        }
    }
}
