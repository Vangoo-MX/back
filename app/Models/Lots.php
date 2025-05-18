<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lots extends Model
{
    protected $fillable = [
        'title',
        'type_lots',
        'developers',
        'status',
        'number_lots',
        'lots_min',
        'lots_max',
        'price_min',
        'price_max',
        'description',
        'availability',
        'financing',
        'type_terrain',
        'slope',
        'id_colonia',
        'id_municipio',
        'id_estado',
        'id_pais',
        'cp',
        'location',
        'num_ext',
        'map_long',
        'map_lat',
        'broad',
        'largue',
        'price_mt2',
        'amenities',
        'initial_fee',
        'commission_percentage',
        'images',
        'id_user',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_lots';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class, 'id_estado', 'id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function colonia(): BelongsTo
    {
        return $this->belongsTo(Colonias::class, 'id_colonia', 'id');
    }

    public function lotHighlight(): HasMany
    {
        return $this->hasMany(LotsHighlights::class, 'id_lots', 'id');
    }
}
