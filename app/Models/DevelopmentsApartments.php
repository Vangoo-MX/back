<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentsApartments extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'post_developments_apartments';

    protected $fillable = [
        'title',
        'price',
        'area',
        'rooms',
        'bathrooms',
        'parkings',
        'num_available',
        'image_plans'
    ];

    protected $casts = [
        'bathrooms' => 'decimal:1',
    ];
}
