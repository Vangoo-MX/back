<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListsUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'list_favorites_list';

    protected $fillable = ['id_user', 'title'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function apartmentsFavorites(): BelongsToMany
    {
        return $this->belongsToMany(ApartmentsFavorites::class);
    }

    public function developmentVerticalsFavorites(): BelongsToMany
    {
        return $this->belongsToMany(DevelopmentsFavorites::class);
    }

    public function developmentHorizontalsFavorites(): BelongsToMany
    {
        return $this->belongsToMany(DevelopmentsHorizontalFavorites::class);
    }

    public function lotsFavorites(): BelongsToMany
    {
        return $this->belongsToMany(LotsFavorites::class);
    }

    public function propertiesFavorites(): BelongsToMany
    {
        return $this->belongsToMany(PropertiesFavorites::class);
    }

    public function terrainsFavorites(): BelongsToMany
    {
        return $this->belongsToMany(TerrainsFavorites::class);
    }
}
