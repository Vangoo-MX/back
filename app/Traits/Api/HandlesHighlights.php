<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null)
    {
        $model = $this->highlightModel;
        $relationship = $this->highlightRelationship;

        $query = $model::with($relationship)
            ->orderBy('num_order', 'asc');

        if (!is_null($municipioId)) {
            $query->where('id_municipio', $municipioId);
        }

        return $query
            ->pluck($relationship)
            ->filter()
            ->values();
    }
}
