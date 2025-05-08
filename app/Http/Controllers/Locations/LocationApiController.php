<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Estados;
use Illuminate\Http\Request;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Collection;

class LocationApiController extends Controller
{
    public function getAllEstados(): Collection
    {
        return Estados::select('id', 'nombre')
            ->orderBy('nombre')
            ->get();
    }

    public function getEstados(): Collection
    {
        return Estados::where(function ($query) {
            $query->whereHas('apartments')
                ->orWhereHas('developmentVertical')
                ->orWhereHas('developmentHorizontal')
                ->orWhereHas('lots')
                ->orWhereHas('properties')
                ->orWhereHas('terrains');
        })
            ->select('id', 'nombre')
            ->distinct()
            ->orderBy('nombre', 'asc')
            ->get();
    }

    public function getMunicipios(int $estadoId): Collection
    {
        return Municipios::where('id_estado', $estadoId)
            ->where(function ($query) {
                $query->has('apartments')
                    ->orHas('developmentVertical')
                    ->orHas('developmentHorizontal')
                    ->orHas('lots')
                    ->orHas('properties')
                    ->orHas('terrains');
            })
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();
    }

    public function getMunicipiosFromEstado(int $estadoId): Collection
    {
        return Municipios::where('id_estado', $estadoId)
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
    }

    public function getColonias(int $municipioId): Collection
    {
        return Colonias::where('id_municipio', $municipioId)
            ->where(function ($query) {
                $query->has('apartments')
                    ->orHas('developmentVertical')
                    ->orHas('developmentHorizontal')
                    ->orHas('lots')
                    ->orHas('properties')
                    ->orHas('terrains');
            })
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();
    }

    public function getColoniasFromMunicipio(int $municipioId): Collection
    {
        return Colonias::where('id_municipio', $municipioId)
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
    }

    public function getHighlightsByMunicipio($municipioId, $tipoHighlight)
    {
        $relacionesPermitidas = [
            'apartments' => 'apartmentsHighlight',
            'terrains' => 'terrainsHighlight',
            'developments-vertical' => 'developmentVerticalHighlight',
            'developments-horizontal' => 'developmentHorizontalHighlight',
            'lots' => 'lotHighlight',
            'properties' => 'propertiesHighlight',
        ];

        if (!array_key_exists($tipoHighlight, $relacionesPermitidas)) {
            return response()->json(['error' => 'Tipo de highlight no válido'], 400);
        }

        return Municipios::where('id', $municipioId)
            ->whereHas($relacionesPermitidas[$tipoHighlight])
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
    }
}
