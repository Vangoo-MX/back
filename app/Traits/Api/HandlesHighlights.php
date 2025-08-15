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

        // UNA SOLA CONSULTA: obtener todos los datos necesarios
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

        // Si hay más municipios que el límite, seleccionar aleatoriamente
        if ($totalMunicipios > $maxTotal) {
            $municipiosSeleccionados = collect($municipios)->shuffle()->take($maxTotal)->toArray();
            $allHighlights = $allHighlights->only($municipiosSeleccionados);
            $totalMunicipios = $maxTotal;
        }

        // Distribución inicial
        $elementosPorMunicipio = floor($maxTotal / $totalMunicipios);
        $elementosExtra = $maxTotal % $totalMunicipios;

        $resultados = collect();
        $municipiosArray = $allHighlights->keys()->toArray();

        // Primera pasada: distribución base
        foreach ($municipiosArray as $index => $municipioId) {
            $limite = $elementosPorMunicipio + ($index < $elementosExtra ? 1 : 0);
            $limite = max(1, $limite);

            $elementos = $allHighlights[$municipioId]
                ->take($limite)
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship});

            $resultados = $resultados->concat($elementos);
        }

        // Segunda pasada: optimización para llenar espacios restantes
        $elementosActuales = $resultados->count();

        if ($elementosActuales < $maxTotal) {
            $espaciosDisponibles = $maxTotal - $elementosActuales;

            foreach ($municipiosArray as $municipioId) {
                if ($espaciosDisponibles <= 0) break;

                $yaObtenidos = $elementosPorMunicipio +
                    (array_search($municipioId, $municipiosArray) < $elementosExtra ? 1 : 0);
                $yaObtenidos = max(1, $yaObtenidos);

                $elementosAdicionales = $allHighlights[$municipioId]
                    ->skip($yaObtenidos)
                    ->take($espaciosDisponibles)
                    ->map(fn($highlight) => $highlight->{$this->highlightRelationship});

                $resultados = $resultados->concat($elementosAdicionales);
                $espaciosDisponibles -= $elementosAdicionales->count();
            }
        }

        return $resultados->take($maxTotal)->shuffle()->values();
    }
}
