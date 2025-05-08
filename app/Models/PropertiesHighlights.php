<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertiesHighlights extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
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
        return $this->belongsTo(Estados::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Properties::class, 'id_property', 'id');
    }
}
