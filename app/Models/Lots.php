<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lots extends Model
{
    protected $fillable = [
        'title',
        'type_lots',
        'developers',
        'status',
        'number_lots',
        'lots_min',
        'lots_max',
        'price_min',
        'price_max',
        'description',
        'availability',
        'financing',
        'type_terrain',
        'slope',
        'id_colonia',
        'id_municipio',
        'id_estado',
        'id_pais',
        'cp',
        'location',
        'num_ext',
        'map_long',
        'map_lat',
        'broad',
        'largue',
        'price_mt2',
        'amenities',
        'initial_fee',
        'commission_percentage',
        'images',
        'id_user',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_lots';
}
