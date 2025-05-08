<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerrainsQueue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'list_terrains_queue';

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
        'map_lat',
        'map_long',
        'antiquity',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
        'services',
        'no_exact_location',
        'images',
        'id_user',
        'views'
    ];

    protected $attributes = [
        'id_pais' => 1,
        'views' => 0
    ];

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
}
