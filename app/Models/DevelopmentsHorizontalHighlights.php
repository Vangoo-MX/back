<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentsHorizontalHighlights extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'id_municipio',
        'id_development',
        'num_order',
    ];

    protected $table = 'post_developments_horizontal_highlights';

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class, 'id_estado', 'id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function horizontal(): BelongsTo
    {
        return $this->belongsTo(DevelopmentsHorizontals::class, 'id_development', 'id');
    }
}
