<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null)
    {
        $model = $this->highlightModel;
        $relationship = $this->highlightRelationship;

        $query = $model::query()
            ->whereHas($relationship)
            ->orderBy('num_order', 'asc');

        if (!is_null($municipioId)) {
            $query->where('id_municipio', $municipioId);
        }

        return $query
            ->with($relationship)
            ->get()
            ->pluck($relationship)
            ->values();
    }
}
