<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estados extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'info_estados';

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipios::class);
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartments::class, 'id_estado', 'id');
    }

    public function developmentVertical(): HasMany
    {
        return $this->hasMany(Developments::class, 'id_estado', 'id');
    }

    public function developmentHorizontal(): HasMany
    {
        return $this->hasMany(DevelopmentsHorizontals::class, 'id_estado', 'id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lots::class, 'id_estado', 'id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Properties::class, 'id_estado', 'id');
    }

    public function terrains(): HasMany
    {
        return $this->hasMany(Terrains::class, 'id_estado', 'id');
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
