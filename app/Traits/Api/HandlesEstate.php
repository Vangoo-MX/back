<?php

namespace App\Traits\Api;

use Illuminate\Support\Collection;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\Colonias;
use App\Models\Estados;
use App\Models\Municipios;
use Illuminate\Database\Eloquent\Builder;

trait HandlesEstate
{
    public function getEstate($id)
    {
        return $this->model::find($id);
    }

    public function getEstateFavorites($id)
    {
        return $this->model::where('id', $id)
            ->get();
    }

    public function getEstateQueue(int $id)
    {
        return $this->modelQueue::find($id);
    }


    public function getUser(int $id)
    {
        return $this->model::select('id', 'title', 'price', 'location', 'views', 'images', 'created_at')
            ->where('id_user', $id)
            ->get();
    }

    public function getUserQueue(int $id): Collection
    {
        return $this->modelQueue::select('id', 'title', 'price', 'location', 'images', 'status_aproved', 'created_at', 'updated_at')
            ->where('id_user', $id)
            ->get();
    }

    /**
     * Obtiene los inmuebles relacionados
     */

    public function getEstateRelated(int $id): Collection
    {
        $estate = $this->findEstate($id);

        if (!$estate) {
            return collect();
        }

        return $this->buildRelatedEstatesQuery($estate, $id)
            ->limit(10)
            ->get();
    }

    private function findEstate(int $id): ?object
    {
        return $this->model::select($this->getRequiredColumns())
            ->find($id);
    }

    private function getRequiredColumns(): array
    {
        $baseColumns = ['id', 'id_municipio'];

        return match (true) {
            property_exists($this, 'priceRangeColumns') => [
                ...$baseColumns,
                $this->priceRangeColumns['min'],
                $this->priceRangeColumns['max']
            ],
            default => [
                ...$baseColumns,
                $this->priceColumn ?? 'price'
            ]
        };
    }

    private function buildRelatedEstatesQuery(object $estate, int $excludeId): Builder
    {
        return $this->model::query()
            ->whereNot('id', $excludeId)
            ->where('id_municipio', $estate->id_municipio)
            ->where(fn($query) => $this->applyPriceFilterRelated($query, $estate));
    }

    private function applyPriceFilterRelated(Builder $query, object $estate): Builder
    {
        return property_exists($this, 'priceRangeColumns')
            ? $this->applyRangePriceFilter($query, $estate)
            : $this->applySinglePriceFilter($query, $estate);
    }

    private function applyRangePriceFilter(Builder $query, object $estate): Builder
    {
        ['min' => $minColumn, 'max' => $maxColumn] = $this->priceRangeColumns;

        $priceRange = $this->calculatePriceRange(
            $estate->{$minColumn},
            $estate->{$maxColumn}
        );

        return $query->where(function ($q) use ($priceRange, $minColumn, $maxColumn) {
            $q->whereBetween($minColumn, $priceRange)
                ->orWhereBetween($maxColumn, $priceRange)
                ->orWhere(function ($subQuery) use ($priceRange, $minColumn, $maxColumn) {
                    $subQuery->where($minColumn, '<=', $priceRange[0])
                        ->where($maxColumn, '>=', $priceRange[1]);
                });
        });
    }

    private function applySinglePriceFilter(Builder $query, object $estate): Builder
    {
        $column = $this->priceColumn ?? 'price';
        $priceRange = $this->calculatePriceRange($estate->{$column});

        return $query->whereBetween($column, $priceRange);
    }

    private function calculatePriceRange(float $minPrice, ?float $maxPrice = null): array
    {
        if ($maxPrice === null) {
            return [
                (int) floor($minPrice * 0.8),
                (int) ceil($minPrice * 1.2)
            ];
        }

        return [
            (int) floor($minPrice * 0.8),
            (int) ceil($maxPrice * 1.2)
        ];
    }

    /**
     * Inicio buscador
     */

    public function getEstateSearch()
    {
        $query = $this->model::query();

        $this->applyLocationFilters($query);

        $this->applyPriceFilter($query);

        return $query->paginate(50);
    }

    private function applyLocationFilters($query): void
    {
        $locationFilters = [
            'estado' => 'id_estado',
            'municipio' => 'id_municipio',
            'colonia' => 'id_colonia'
        ];

        foreach ($locationFilters as $param => $column) {
            if ($value = request($param)) {
                $query->where($column, $value);
            }
        }
    }

    private function applyPriceFilter($query): void
    {
        if (!request()->has('min') && !request()->has('max')) {
            return;
        }

        $min = (float)request('min', 0);
        $max = (float)request('max', 0);

        if (property_exists($this, 'priceRangeColumns')) {
            $minColumn = $this->priceRangeColumns['min'];
            $maxColumn = $this->priceRangeColumns['max'];

            if ($max > 0) {
                $query->where(function ($q) use ($min, $max, $minColumn, $maxColumn) {
                    $q->whereBetween($minColumn, [$min, $max])
                        ->orWhereBetween($maxColumn, [$min, $max])
                        ->orWhere(function ($subQuery) use ($min, $max, $minColumn, $maxColumn) {
                            $subQuery->where($minColumn, '<=', $min)
                                ->where($maxColumn, '>=', $max);
                        });
                });
            } else {
                $query->where($minColumn, '<=', $min)
                    ->where($maxColumn, '>=', $min);
            }
        } else {
            $column = property_exists($this, 'priceColumn') ? $this->priceColumn : 'price';
            $query->where(function ($q) use ($min, $max, $column) {
                $max > 0
                    ? $q->whereBetween($column, [$min, $max])
                    : $q->where($column, '>=', $min);
            });
        }
    }

    protected function getLocation(int $coloniaId, int $municipioId, int $estadoId): string
    {
        $colonia = Colonias::find($coloniaId);
        $municipio = Municipios::find($municipioId);
        $estado = Estados::find($estadoId);

        return collect([
            $colonia->nombre ?? null,
            $municipio->nombre ?? null,
            $estado->nombre ?? null,
        ])->filter()->join(', ');
    }

    public function deleteEstateQueue(int $id)
    {
        try {
            $estateQueue = $this->modelQueue::findOrFail($id);
            $directoryPath = public_path("storage/img/postsqueue/{$this->directory}/{$estateQueue->id}");

            if (File::exists($directoryPath)) {
                File::deleteDirectory($directoryPath);
            }

            $estateQueue->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            Log::error("Error deleting terrain queue: {$e->getMessage()}");
            return response()->json(
                ['error' => 'Failed to delete resource'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function deleteEstate(int $id)
    {
        try {
            $estate = $this->model::findOrFail($id);
            $directoryPath = public_path("storage/img/posts/{$this->directory}/{$estate->id}");

            if (File::exists($directoryPath)) {
                File::deleteDirectory($directoryPath);
            }

            $estate->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            Log::error("Error deleting resource queue: {$e->getMessage()}");
            return response()->json(
                ['error' => 'Failed to delete resource'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
