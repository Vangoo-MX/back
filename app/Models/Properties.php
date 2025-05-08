<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Properties extends Model
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
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_properties';

    protected $casts = [
        'bathrooms' => 'decimal:1',
    ];

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

    public function propertiesHighlights(): HasMany
    {
        return $this->hasMany(PropertiesHighlights::class);
    }
}
