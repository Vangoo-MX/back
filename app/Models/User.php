<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    protected $table = 'app_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'tel',
        'contact_preference',
        'contact_schedule',
        'biography',
        'rol',
        'profile_image'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'rol' => UserRole::class,
    ];

    protected function name(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucwords($value),
            set: fn($value) => strtolower($value)
        );
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartments::class);
    }

    public function developmentVerticals(): HasMany
    {
        return $this->hasMany(Developments::class);
    }

    public function developmentHorizontals(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontals::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lots::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Properties::class);
    }

    public function terrains(): HasMany
    {
        return $this->hasMany(Terrains::class);
    }

    public function listUser(): HasMany
    {
        return $this->hasMany(ListsUser::class, 'id_user', 'id');
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(Agenda::class, 'id_user', 'id');
    }
}
