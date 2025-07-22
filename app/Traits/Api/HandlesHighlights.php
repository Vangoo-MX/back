<?php

namespace App\Traits\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

trait HandlesHighlights
{
    //     public function getHighlitedItems(?int $municipioId = null)
    //     {
    //         $model = $this->highlightModel;
    //         $relationship = $this->highlightRelationship;

    //         $query = $model::with($relationship)
    //             ->orderBy('num_order', 'asc');

    //         if (!is_null($municipioId)) {
    //             $query->where('id_municipio', $municipioId);
    //         }

    //         return $query->get()
    //             ->map(fn($highlight) => $highlight->{$relationship})
    //             ->filter()
    //             ->values();
    //     }

    public function getHighlightedItems(?int $municipioId = null): Collection
    {
        /* 1. Derive table & key names dynamically so it keeps working
          even if you change them later in the migration               */
        $highlightTable   = $this->highlightModel->getTable();
        $relatedModel     = $this->highlightModel->{$this->highlightRelationship}()->getRelated();
        $relatedTable     = $relatedModel->getTable();
        $foreignKey       = $this->highlightModel->{$this->highlightRelationship}()->getForeignKeyName(); // highlight_id
        $localKey         = $this->highlightModel->{$this->highlightRelationship}()->getLocalKeyName();   // id

        /* 2. Build a single JOIN query selecting ONLY the columns we need  */
        $query = $relatedModel->newQuery()
            ->select($relatedTable . '.*')          // or list exact columns
            ->join($highlightTable, $highlightTable . '.' . $localKey, '=', $relatedTable . '.' . $foreignKey)
            ->orderBy($highlightTable . '.num_order');   // keeps original ordering

        /* 3. Optional municipality filter                                    */
        if ($municipioId !== null) {
            $query->where($highlightTable . '.id_municipio', $municipioId);
        }

        /* 4. Optional safety net – avoid runaway memory on large datasets    */
        // $query->limit(100);

        return $query->get();
    }
}
