<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'info_municipios';

    public function estados(): BelongsTo
    {
        return $this->belongsTo(Estados::class);
    }

    public function colonias(): HasMany
    {
        return $this->hasMany(Colonias::class);
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartments::class, 'id_municipio', 'id');
    }

    public function developmentVertical(): HasMany
    {
        return $this->hasMany(Developments::class, 'id_municipio', 'id');
    }

    public function developmentHorizontal(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontals::class, 'id_municipio', 'id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lots::class, 'id_municipio', 'id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Properties::class, 'id_municipio', 'id');
    }

    public function terrains(): HasMany
    {
        return $this->hasMany(Terrains::class, 'id_municipio', 'id');
    }

    public function apartmentsHighlight(): HasMany
    {
        return $this->hasMany(ApartmentsHighlights::class, 'id_municipio', 'id');
    }

    public function developmentVerticalHighlight(): HasMany
    {
        return $this->hasMany(DevelopmentsHighlights::class, 'id_municipio', 'id');
    }

    public function developmentHorizontalHighlight(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontalHighlights::class, 'id_municipio', 'id');
    }

    public function lotHighlight(): HasMany
    {
        return $this->hasMany(LotsHighlights::class, 'id_municipio', 'id');
    }

    public function propertiesHighlight(): HasMany
    {
        return $this->hasMany(PropertiesHighlights::class, 'id_municipio', 'id');
    }

    public function terrainsHighlight(): HasMany
    {
        return $this->hasMany(TerrainsHighlights::class, 'id_municipio', 'id');
    }

    public function apartmentsQueue(): HasMany
    {
        return $this->hasMany(ApartmentsQueue::class, 'id_colonia', 'id');
    }

    public function propertiesQueue(): HasMany
    {
        return $this->hasMany(PropertiesQueue::class, 'id_colonia', 'id');
    }

    public function TerrainsQueue(): HasMany
    {
        return $this->hasMany(TerrainsQueue::class, 'id_colonia', 'id');
    }
}
