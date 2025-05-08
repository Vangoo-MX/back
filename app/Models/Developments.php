<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Developments extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_developments';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class);
    }

    public function colonia(): BelongsTo
    {
        return $this->belongsTo(Colonias::class);
    }

    public function developmentVerticalApartments(): HasMany
    {
        return $this->hasMany(DevelopmentsApartments::class);
    }

    public function developmentVerticalHighlight(): HasMany
    {
        return $this->hasMany(DevelopmentsHighlights::class);
    }
}
