<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Terrains extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'price',
        'num_int',
        'num_ext',
        'street',
        'id_colonia',
        'id_municipio',
        'id_estado',
        'id_pais',
        'cp',
        'location',
        'area_terrain',
        'parkings',
        'description',
        'map',
        'map_long',
        'map_lat',
        'antiquity',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
        'services',
        'views',
        'no_exact_location',
        'images',
        'id_user',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_terrains';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

    public function colonia(): BelongsTo
    {
        return $this->belongsTo(Colonias::class);
    }

    public function terrainsHighlights(): HasMany
    {
        return $this->hasMany(TerrainsHighlights::class, 'id_terrain', 'id');
    }
}
