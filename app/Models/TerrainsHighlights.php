<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerrainsHighlights extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'post_terrains_highlights';

    public function apartment()
    {
        return $this->belongsTo(Terrains::class, 'id_property');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'id_estado');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'id_municipio');
    }
}
