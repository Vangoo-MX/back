<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null, int $maxTotal = 10)
    {
        if (!is_null($municipioId)) {
            return $this->highlightModel::with($this->highlightRelationship)
                ->where('id_municipio', $municipioId)
                ->orderBy('num_order', 'asc')
                ->get()
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
                ->filter()
                ->values();
        }

        $allHighlights = $this->highlightModel::with($this->highlightRelationship)
            ->orderBy('id_municipio', 'asc')
            ->orderBy('num_order', 'asc')
            ->get()
            ->filter(fn($highlight) => $highlight->{$this->highlightRelationship} !== null)
            ->groupBy('id_municipio');

        if ($allHighlights->isEmpty()) {
            return collect();
        }

        $municipios = $allHighlights->keys()->toArray();
        $totalMunicipios = count($municipios);

        if ($totalMunicipios > $maxTotal) {
            $municipiosSeleccionados = collect($municipios)->shuffle()->take($maxTotal)->toArray();
            $allHighlights = $allHighlights->only($municipiosSeleccionados);
            $totalMunicipios = $maxTotal;
        }

        $elementosPorMunicipio = floor($maxTotal / $totalMunicipios);
        $elementosExtra = $maxTotal % $totalMunicipios;

        $resultados = collect();
        $municipiosArray = $allHighlights->keys()->toArray();

        foreach ($municipiosArray as $index => $municipioId) {
            $limite = $elementosPorMunicipio + ($index < $elementosExtra ? 1 : 0);
            $limite = max(1, $limite);

            $elementos = $allHighlights[$municipioId]
                ->take($limite)
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship});

            $resultados = $resultados->concat($elementos);
        }

        $elementosActuales = $resultados->count();

        if ($elementosActuales < $maxTotal) {
            $espaciosDisponibles = $maxTotal - $elementosActuales;

            foreach ($municipiosArray as $municipioId) {
                if ($espaciosDisponibles <= 0) break;

                $obtenidos = $elementosPorMunicipio +
                    (array_search($municipioId, $municipiosArray) < $elementosExtra ? 1 : 0);
                $obtenidos = max(1, $obtenidos);

                $elementosAdicionales = $allHighlights[$municipioId]
                    ->skip($obtenidos)
                    ->take($espaciosDisponibles)
                    ->map(fn($highlight) => $highlight->{$this->highlightRelationship});

                $resultados = $resultados->concat($elementosAdicionales);
                $espaciosDisponibles -= $elementosAdicionales->count();
            }
        }

        return $resultados->take($maxTotal)->shuffle()->values();
    }
}
