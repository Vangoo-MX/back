<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'id_development',
        'num_order',
    ];
    protected $table = 'post_lots_highlights';
}
