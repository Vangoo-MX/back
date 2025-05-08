<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentsHighlights extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'id_municipio',
        'id_development',
        'num_order',
    ];
    protected $table = 'post_developments_highlights';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

    public function developmentVertical(): BelongsTo
    {
        return $this->belongsTo(Developments::class, 'id_development', 'id');
    }
}
