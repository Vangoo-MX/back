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

        $municipios = $this->highlightModel::select('id_municipio')
            ->distinct()
            ->pluck('id_municipio')
            ->toArray();

        $totalMunicipios = count($municipios);

        if ($totalMunicipios === 0) {
            return collect();
        }

        if ($totalMunicipios > $maxTotal) {
            $municipios = collect($municipios)->shuffle()->take($maxTotal)->toArray();
            $totalMunicipios = $maxTotal;
        }

        $elementosPorMunicipio = floor($maxTotal / $totalMunicipios);
        $elementosRestantes = $maxTotal % $totalMunicipios;

        $resultados = collect();
        $elementosObtenidos = [];

        foreach ($municipios as $index => $municipio) {
            $limite = $elementosPorMunicipio + ($index < $elementosRestantes ? 1 : 0);
            $limite = max(1, $limite);

            $elementos = $this->highlightModel::with($this->highlightRelationship)
                ->where('id_municipio', $municipio)
                ->orderBy('num_order', 'asc')
                ->take($limite)
                ->get()
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
                ->filter();

            $resultados = $resultados->concat($elementos);
            $elementosObtenidos[$municipio] = $elementos->count();
        }

        $elementosActuales = $resultados->count();

        if ($elementosActuales < $maxTotal) {
            $espaciosDisponibles = $maxTotal - $elementosActuales;

            foreach ($municipios as $municipio) {
                if ($espaciosDisponibles <= 0) break;

                $yaObtenidos = $elementosObtenidos[$municipio];

                $elementosAdicionales = $this->highlightModel::with($this->highlightRelationship)
                    ->where('id_municipio', $municipio)
                    ->orderBy('num_order', 'asc')
                    ->skip($yaObtenidos)
                    ->take($espaciosDisponibles)
                    ->get()
                    ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
                    ->filter();

                $resultados = $resultados->concat($elementosAdicionales);
                $espaciosDisponibles -= $elementosAdicionales->count();
            }
        }

        return $resultados->take($maxTotal)->shuffle()->values();
    }
}
