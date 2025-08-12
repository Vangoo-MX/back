<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotsHighlights extends Model
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
        'id_lot',
        'num_order',
    ];
    protected $table = 'post_lots_highlights';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class, 'id_estado', 'id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lots::class, 'id_lot', 'id');
    }
}
