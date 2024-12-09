<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
    protected $table = 'post_developments_highlights';
}
