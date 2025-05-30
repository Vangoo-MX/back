<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartments extends Model
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
        'area',
        'bathrooms',
        'rooms',
        'dev_type',
        'parkings',
        'description',
        'map',
        'map_long',
        'map_lat',
        'antiquity',
        'amenities',
        'floor',
        'price_maintenance',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
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
    protected $table = 'post_apartments';

    protected $casts = [
        'bathrooms' => 'decimal:1',
        'images' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
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

    public function apartmentHighlights(): HasMany
    {
        return $this->hasMany(ApartmentsHighlights::class, 'id_apartment', 'id');
    }
}
