<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LotsFavorites extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'list_favorites_lots';

    protected $fillable = [
        'id_user',
        'id_property',
        'id_list'
    ];

    public function listUser(): BelongsToMany
    {
        return $this->belongsToMany(ListsUser::class, 'id_list', 'id');
    }
}
