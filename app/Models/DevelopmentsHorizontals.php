<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentsHorizontals extends Model
{
    protected $table = 'post_developments_horizontal';

    protected $fillable = [
        'title',
        'status',
        'price_min',
        'price_max',
        'description',
        'availability',
        'financing',
        'mode',
        'id_estado',
        'id_municipio',
        'id_colonia',
        'cp',
        'street',
        'num_ext',
        'location',
        'map_lat',
        'map_long',
        'area',
        'amenities',
        'commission_percentage',
        'id_user',
        'images',
    ];

    protected $casts = [
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

    public function developmentHorizontalApartments(): HasMany
    {
        return $this->hasMany(DevelopmentsApartments::class, 'id_development', 'id');
    }

    public function developmentHorizontalHighlight(): HasMany
    {
        return $this->hasMany(DevelopmentsHighlights::class, 'id_development', 'id');
    }
}
