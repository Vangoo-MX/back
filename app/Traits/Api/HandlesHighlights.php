<?php

namespace App\Traits\Api;

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

    public function getHighlightedItems(?int $municipioId = null): \Illuminate\Support\Collection
    {
        /* 1. Instancias */
        $highlightModel = app($this->highlightModel);            // PropertiesHighlights
        $relation       = $highlightModel->{$this->highlightRelationship}(); // ->property()
        $relatedModel   = $relation->getRelated();               // Properties

        /* 2. Tablas y columnas */
        $highlightTable = $highlightModel->getTable();           // post_properties_highlights
        $relatedTable   = $relatedModel->getTable();             // post_properties
        $foreignKey = $relation->getForeignKeyName();        // "post_properties_highlights.id_property"
        $foreignKey = last(explode('.', $foreignKey));       // "id_property"
        $localKey   = $relation->getOwnerKeyName();          // "id"

        /* 3. Query con JOIN */
        $query = $relatedModel->newQuery()
            ->select($relatedTable . '.*')
            ->join($highlightTable, "{$highlightTable}.{$foreignKey}", '=', "{$relatedTable}.{$localKey}")
            ->orderBy("{$highlightTable}.num_order");

        if ($municipioId !== null) {
            $query->where("{$highlightTable}.id_municipio", $municipioId);
        }

        /* 4. Ejecutar y devolver */
        return $query->get();
    }
}
