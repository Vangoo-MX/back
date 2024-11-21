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

    public function apartment()
    {
        return $this->belongsTo(Apartments::class, 'id_property');
    }
}
