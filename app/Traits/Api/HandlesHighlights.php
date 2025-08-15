<?php

namespace App\Traits\Api;

trait HandlesHighlights
{
    public function getHighlitedItems(?int $municipioId = null, int $maxTotal = 10)
    {
        // Si se especifica un municipio, comportamiento original
        if (!is_null($municipioId)) {
            return $this->highlightModel::with($this->highlightRelationship)
                ->where('id_municipio', $municipioId)
                ->orderBy('num_order', 'asc')
                ->get()
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
                ->filter()
                ->values();
        }

        // Para todos los municipios: distribución balanceada

        // 1. Obtener todos los municipios únicos
        $municipios = $this->highlightModel::select('id_municipio')
            ->distinct()
            ->pluck('id_municipio')
            ->toArray();

        $totalMunicipios = count($municipios);

        if ($totalMunicipios === 0) {
            return collect();
        }

        // OPCIÓN A: Garantizar al menos 1 elemento por municipio hasta el límite
        if ($totalMunicipios > $maxTotal) {
            // Si hay más municipios que el límite, seleccionar municipios aleatoriamente
            $municipios = collect($municipios)->shuffle()->take($maxTotal)->toArray();
            $totalMunicipios = $maxTotal;
        }

        // 2. Calcular elementos por municipio
        $elementosPorMunicipio = floor($maxTotal / $totalMunicipios);
        $elementosExtra = $maxTotal % $totalMunicipios;

        $resultados = collect();

        // 3. Obtener elementos de cada municipio
        foreach ($municipios as $index => $municipio) {
            // Los primeros municipios obtienen un elemento extra si hay residuo
            $limite = $elementosPorMunicipio + ($index < $elementosExtra ? 1 : 0);

            // Asegurar al menos 1 elemento si es posible
            $limite = max(1, $limite);

            $elementos = $this->highlightModel::with($this->highlightRelationship)
                ->where('id_municipio', $municipio)
                ->orderBy('num_order', 'asc')
                ->take($limite)
                ->get()
                ->map(fn($highlight) => $highlight->{$this->highlightRelationship})
                ->filter();

            $resultados = $resultados->concat($elementos);

            // Verificar si ya alcanzamos el límite
            if ($resultados->count() >= $maxTotal) {
                break;
            }
        }

        return $resultados->take($maxTotal)->values();
    }
}
