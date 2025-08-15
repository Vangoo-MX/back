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

        // Para todos los municipios: distribución balanceada y optimizada

        // 1. Obtener todos los municipios únicos
        $municipios = $this->highlightModel::select('id_municipio')
            ->distinct()
            ->pluck('id_municipio')
            ->toArray();

        $totalMunicipios = count($municipios);

        if ($totalMunicipios === 0) {
            return collect();
        }

        // Si hay más municipios que el límite, seleccionar municipios aleatoriamente
        if ($totalMunicipios > $maxTotal) {
            $municipios = collect($municipios)->shuffle()->take($maxTotal)->toArray();
            $totalMunicipios = $maxTotal;
        }

        // 2. Primera pasada: distribución base (al menos 1 por municipio)
        $elementosPorMunicipio = floor($maxTotal / $totalMunicipios);
        $elementosRestantes = $maxTotal % $totalMunicipios;

        $resultados = collect();
        $elementosObtenidos = [];

        // 3. Obtener elementos iniciales de cada municipio
        foreach ($municipios as $index => $municipio) {
            $limite = $elementosPorMunicipio + ($index < $elementosRestantes ? 1 : 0);
            $limite = max(1, $limite); // Al menos 1 por municipio

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

        // 4. Segunda pasada: llenar espacios restantes si no hemos llegado al máximo
        $elementosActuales = $resultados->count();

        if ($elementosActuales < $maxTotal) {
            $espaciosDisponibles = $maxTotal - $elementosActuales;

            // Intentar obtener más elementos de cada municipio en orden
            foreach ($municipios as $municipio) {
                if ($espaciosDisponibles <= 0) break;

                $yaObtenidos = $elementosObtenidos[$municipio];

                // Obtener elementos adicionales saltando los ya obtenidos
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

        return $resultados->take($maxTotal)->values();
    }
}
