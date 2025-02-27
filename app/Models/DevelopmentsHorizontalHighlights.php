<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
