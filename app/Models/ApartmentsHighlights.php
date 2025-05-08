<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentsHighlights extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'post_apartments_highlights';

    protected $fillable = [
        'id_estado',
        'id_municipio',
        'id_property',
        'num_order',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartments::class, 'id_property', 'id');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipios::class);
    }
}
