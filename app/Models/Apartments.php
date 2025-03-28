<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartments extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'price',
        'num_int',
        'num_ext',
        'street',
        'id_colonia',
        'id_municipio',
        'id_estado',
        'id_pais',
        'cp',
        'location',
        'area',
        'bathrooms',
        'rooms',
        'dev_type',
        'parkings',
        'description',
        'map',
        'map_long',
        'map_lat',
        'antiquity',
        'amenities',
        'floor',
        'price_maintenance',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
        'views',
        'no_exact_location',
        'images',
        'id_user',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_apartments';

    protected $casts = [
        'bathrooms' => 'decimal:1',
    ];
}
