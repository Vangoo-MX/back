<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Properties extends Model
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
        'arrea_terrain',
        'bathrooms',
        'rooms',
        'type',
        'dev_type',
        'parkings',
        'description',
        'map',
        'antiquity',
        'amenities',
        'floor',
        'price_maintenance',
        'operation_type',
        'price_m2',
        'sell_type',
        'share_conditions',
        'services',
        'views',
        'no_exact_location',
        'images',
        'id_user'
    ];
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_properties';

}
