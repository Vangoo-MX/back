<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model
{
    public $timestamps = false;
    protected $table = 'list_agenda';

    protected $fillable = [
        'id_user',
        'name',
        'phone',
        'email',
        'address',
        'credit_score',
        'notes',
        'mensaje_leido',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function agendaDocs(): HasMany
    {
        return $this->hasMany(AgendaDocs::class, 'id_agenda', 'id');
    }

    public function getUuidAttribute()
    {
        return $this->user->uuid;
    }
}
