<?php

namespace App\Traits\Api;

use App\Models\PropertiesHighlights;
use App\Models\ApartmentsHighlights;
use App\Models\TerrainsHighlights;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\LotsHighlights;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null)
    {
        $model = $this->highlightModel;
        $relationship = $this->highlightRelationship;
        $foreignKey = (new $model)->{$relationship}()->getForeignKeyName();

        $fields = match ($model) {
            PropertiesHighlights::class => ['id', 'title', 'price', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
            ApartmentsHighlights::class => ['id', 'title', 'price', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
            TerrainsHighlights::class => ['id', 'title', 'price', 'location', 'description', 'parkings', 'area', 'images'],
            DevelopmentsHighlights::class => ['id', 'title', 'price_min', 'location', 'description', 'images', 'status', 'mode'],
            DevelopmentsHorizontalHighlights::class => ['id', 'title', 'price_min', 'location', 'description', 'images', 'status', 'mode'],
            LotsHighlights::class => ['id', 'title', 'price_min', 'location', 'description', 'slope', 'images'],
        };

        $query = $model::select($foreignKey, 'num_order')
            ->when(!is_null($municipioId), fn($q) => $q->where('id_municipio', $municipioId))
            ->orderBy('num_order')
            ->with([$relationship => fn($q) => $q->select($fields)]);

        return $query->get()
            ->map(fn($highlight) => $highlight->{$relationship})
            ->filter()
            ->values();
    }
}
