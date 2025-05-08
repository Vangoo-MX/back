<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentsHorizontalApartments extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'post_developments_horizontal_apartments';

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

    public function developmentHorizontal(): BelongsTo
    {
        return $this->belongsTo(Developments::class, 'id_developments_horizontal');
    }
}
