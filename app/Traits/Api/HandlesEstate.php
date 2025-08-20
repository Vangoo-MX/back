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
use Illuminate\Support\Facades\Request;

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
        $estate = $this->model::select($this->getRequiredColumns())->find($id);

        return $estate
            ? $this->model::query()
            ->whereNot('id', $id)
            ->where('id_municipio', $estate->id_municipio)
            ->where(fn($q) => $this->applyPriceFilterRelated($q, $estate))
            ->limit(10)
            ->get()
            : collect();
    }

    private function getRequiredColumns(): array
    {
        $base = ['id', 'id_municipio'];

        return property_exists($this, 'priceRangeColumns')
            ? [...$base, ...array_values($this->priceRangeColumns)]
            : [...$base, $this->priceColumn ?? 'price'];
    }

    private function applyPriceFilterRelated($query, object $estate)
    {
        if (property_exists($this, 'priceRangeColumns')) {
            ['min' => $min, 'max' => $max] = $this->priceRangeColumns;
            [$minPrice, $maxPrice] = [(int) floor($estate->{$min} * 0.8), (int) ceil($estate->{$max} * 1.2)];

            return $query->where(
                fn($q) => $q
                    ->whereBetween($min, [$minPrice, $maxPrice])
                    ->orWhereBetween($max, [$minPrice, $maxPrice])
                    ->orWhere(fn($sub) => $sub->where($min, '<=', $minPrice)->where($max, '>=', $maxPrice))
            );
        }

        $column = $this->priceColumn ?? 'price';
        $range = [(int) floor($estate->{$column} * 0.8), (int) ceil($estate->{$column} * 1.2)];

        return $query->whereBetween($column, $range);
    }

    /**
     * Inicio buscador
     */

    protected array $locationFilters = [
        'estado' => 'id_estado',
        'municipio' => 'id_municipio',
        'colonia' => 'id_colonia'
    ];

    public function getEstateSearch(?Request $request = null)
    {
        $request ??= request();
        $query = $this->model::query();

        collect($this->locationFilters)->each(
            fn($column, $param) => $request->filled($param) && $query->where($column, $request->input($param))
        );

        if ($request->hasAny(['min', 'max'])) {
            $min = max(0, (float) $request->input('min', 0));
            $max = max(0, (float) $request->input('max', 0));

            if (isset($this->priceRangeColumns)) {
                [$minCol, $maxCol] = [$this->priceRangeColumns['min'], $this->priceRangeColumns['max']];

                $query->where(
                    fn($q) => $max > 0
                        ? $q->whereBetween($minCol, [$min, $max])
                        ->orWhereBetween($maxCol, [$min, $max])
                        ->orWhere(fn($sq) => $sq->where($minCol, '<=', $min)->where($maxCol, '>=', $max))
                        : $q->where($maxCol, '>=', $min)
                );
            } else {
                $column = $this->priceColumn ?? 'price';
                $query->where(
                    fn($q) => $max > 0
                        ? $q->whereBetween($column, [$min, $max])
                        : $q->where($column, '>=', $min)
                );
            }
        }

        return $query->paginate(50);
    }

    protected function getLocation(int $coloniaId, int $municipioId, int $estadoId): string
    {
        return collect([
            Colonias::find($coloniaId)?->nombre,
            Municipios::find($municipioId)?->nombre,
            Estados::find($estadoId)?->nombre,
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
