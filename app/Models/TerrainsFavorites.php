<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TerrainsFavorites extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'list_favorites_terrains';

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
