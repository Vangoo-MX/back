<?php

namespace App\Traits\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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

    public function getHighlightedItems(?int $municipioId = null): Collection
    {
        /* 1. Obtenemos los nombres de tabla y relación a partir de los strings */
        $highlightModel = $this->highlightModel;           // 'App\Models\PropertiesHighlights'
        $relatedModel   = $highlightModel::make()
            ->{$this->highlightRelationship}()
            ->getRelated();

        $highlightTable = $highlightModel::make()->getTable();
        $relatedTable   = $relatedModel->getTable();
        $foreignKey     = $highlightModel::make()
            ->{$this->highlightRelationship}()
            ->getForeignKeyName();           // propiedad_id
        $localKey       = $highlightModel::make()
            ->{$this->highlightRelationship}()
            ->getOwnerKeyName();             // id  (antes getLocalKeyName)

        /* 2. Query con JOIN único */
        $query = $relatedModel->newQuery()
            ->select($relatedTable . '.*')
            ->join($highlightTable, $highlightTable . '.' . $localKey, '=', $relatedTable . '.' . $foreignKey)
            ->orderBy($highlightTable . '.num_order');

        if ($municipioId !== null) {
            $query->where($highlightTable . '.id_municipio', $municipioId);
        }

        return $query->get();
    }
}
