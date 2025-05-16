<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DevelopmentsHorizontalFavorites extends Model
{
    public $timestamps = false;
    protected $table = 'list_favorites_developments_horizontal';

    protected $fillable = [
        'id_user',
        'id_development',
        'id_list'
    ];

    public function listUser(): BelongsToMany
    {
        return $this->belongsToMany(ListsUser::class, 'id_list', 'id');
    }

    public static function getRelatedModelClass(): string
    {
        return DevelopmentsHorizontals::class;
    }
}
