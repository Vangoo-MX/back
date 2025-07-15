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
use Illuminate\Support\Str;

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
        'profile_image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'rol' => UserRole::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

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
        return $this->hasMany(Apartments::class, 'id', 'id_user');
    }

    public function developmentVerticals(): HasMany
    {
        return $this->hasMany(Developments::class, 'id', 'id_user');
    }

    public function developmentHorizontals(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontals::class, 'id', 'id_user');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lots::class, 'id', 'id_user');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Properties::class, 'id', 'id_user');
    }

    public function terrains(): HasMany
    {
        return $this->hasMany(Terrains::class, 'id', 'id_user');
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
