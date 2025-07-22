<?php

namespace App\Traits\Api;

use App\Models\ApartmentsHighlights;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\LotsHighlights;
use App\Models\PropertiesHighlights;
use App\Models\TerrainsHighlights;

trait HandlesHighlights
{
    // public function getHighlitedItems(?int $municipioId = null)
    // {
    //     $model = $this->highlightModel;
    //     $relationship = $this->highlightRelationship;

    //     $query = $model::with($relationship)
    //         ->orderBy('num_order', 'asc');

    //     if (!is_null($municipioId)) {
    //         $query->where('id_municipio', $municipioId);
    //     }

    //     return $query->get()
    //         ->map(fn($highlight) => $highlight->{$relationship})
    //         ->filter()
    //         ->values();
    // }

    public function getHighlitedItems(?int $municipioId = null)
    {
        $fields = match ($this->highlightModel) {
            PropertiesHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
            ApartmentsHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
            TerrainsHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'parkings', 'area', 'images'],
            DevelopmentsHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'images', 'status', 'mode'],
            DevelopmentsHorizontalHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'images', 'status', 'mode'],
            LotsHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'slope', 'images'],
        };

        return $this->highlightModel::query()
            ->when($municipioId, fn($q) => $q->where('id_municipio', $municipioId))
            ->orderBy('num_order')
            ->with([
                $this->highlightRelationship => fn($q) =>
                $q->select($fields)
            ])
            ->cursor()
            ->pluck($this->highlightRelationship)
            ->filter()
            ->values();
    }
}
