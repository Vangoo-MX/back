<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentsHorizontals extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_developments_horizontal';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados::class, 'id_estado', 'id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function colonia(): BelongsTo
    {
        return $this->belongsTo(Colonias::class, 'id_colonia', 'id');
    }

    public function developmentHorizontalApartments(): HasMany
    {
        return $this->hasMany(DevelopmentsApartments::class, 'id_development', 'id');
    }

    public function developmentHorizontalHighlight(): HasMany
    {
        return $this->hasMany(DevelopmentsHighlights::class, 'id_development', 'id');
    }
}
