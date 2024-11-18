<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentsFavorites extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'list_favorites_apartments';
}
