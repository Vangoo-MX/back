<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertiesHighlights extends Model
{
    public $timestamps = false;
    protected $table = 'post_properties_highlights';

    protected $fillable = [
        'id_estado',
        'id_municipio',
        'id_property',
        'num_order',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class, 'id_estado', 'id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Properties::class, 'id_property', 'id')
            ->select('id', 'title', 'price', 'location', 'area', 'bathrooms', 'rooms', 'parkings', 'description', 'images');
    }
}
