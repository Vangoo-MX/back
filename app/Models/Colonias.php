<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colonias extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'info_colonias';

    public function municipios(): BelongsTo
    {
        return $this->belongsTo(Municipios::class, 'id_municipio', 'id');
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartments::class, 'id_colonia', 'id');
    }

    public function developmentVertical(): HasMany
    {
        return $this->hasMany(Developments::class, 'id_colonia', 'id');
    }

    public function developmentHorizontal(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontals::class, 'id_colonia', 'id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lots::class, 'id_colonia', 'id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Properties::class, 'id_colonia', 'id');
    }

    public function terrains(): HasMany
    {
        return $this->hasMany(Terrains::class, 'id_colonia', 'id');
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
