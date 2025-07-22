<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null)
    {
        // $fields = match ($this->highlightModel) {
        //     PropertiesHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
        //     ApartmentsHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'rooms', 'bathrooms', 'parkings', 'area', 'images'],
        //     TerrainsHighlights::class => ['id', 'title', 'price', 'num_order', 'location', 'description', 'parkings', 'area', 'images'],
        //     DevelopmentsHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'images', 'status', 'mode'],
        //     DevelopmentsHorizontalHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'images', 'status', 'mode'],
        //     LotsHighlights::class => ['id', 'title', 'price_min', 'num_order', 'location', 'description', 'slope', 'images'],
        // };

        $model = $this->highlightModel;
        $relationship = $this->highlightRelationship;

        $query = $model::with($relationship)
            ->orderBy('num_order', 'asc');

        if (!is_null($municipioId)) {
            $query->where('id_municipio', $municipioId);
        }

        return $query->get()
            ->map(fn($highlight) => $highlight->{$relationship})
            ->filter()
            ->values();
    }
}
