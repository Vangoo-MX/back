<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentsQueue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'list_apartments_queue';

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
        'area',
        'bathrooms',
        'rooms',
        'parkings',
        'description',
        'map',
        'map_long',
        'map_lat',
        'antiquity',
        'amenities',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
        'views',
        'no_exact_location',
        'images',
        'id_user',
        'status',
        'floor',
        'dev_type',
        'price_maintenance',
    ];

    protected $casts = [
        'bathrooms' => 'decimal:1',
    ];

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
}
