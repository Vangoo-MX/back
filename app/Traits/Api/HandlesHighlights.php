<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null)
    {
        $query = $this->highlightModel::with($this->highlightRelationship)
            ->orderBy('num_order', 'asc');

        if (!is_null($municipioId)) {
            $query->where('id_municipio', $municipioId);
        }

        return $query->get()
            ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
            ->filter()
            ->values();
    }
}
