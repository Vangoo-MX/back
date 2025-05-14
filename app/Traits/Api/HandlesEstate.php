<?php

namespace App\Traits\Api;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Illuminate\Http\Response;

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

    public function getEstateRelated(int $id): Collection
    {
        $estate = $this->model::find($id);

        if (!$estate) {
            return collect();
        }

        $query = $this->model::query()
            ->whereNot('id', $id)
            ->where('id_municipio', $estate->id_municipio);

        if (property_exists($this, 'priceRangeColumns')) {
            $minColumn = $this->priceRangeColumns['min'];
            $maxColumn = $this->priceRangeColumns['max'];

            $minPrice = floor($estate->{$minColumn} * 0.8);
            $maxPrice = ceil($estate->{$maxColumn} * 1.2);

            $query->where(function ($q) use ($minPrice, $maxPrice, $minColumn, $maxColumn) {
                $q->whereBetween($minColumn, [$minPrice, $maxPrice])
                    ->orWhereBetween($maxColumn, [$minPrice, $maxPrice])
                    ->orWhere(function ($subQuery) use ($minPrice, $maxPrice, $minColumn, $maxColumn) {
                        $subQuery->where($minColumn, '<=', $minPrice)
                            ->where($maxColumn, '>=', $maxPrice);
                    });
            });
        } else {
            $column = property_exists($this, 'priceColumn') ? $this->priceColumn : 'price';
            $minPrice = $estate->{$column} * 0.8;
            $maxPrice = $estate->{$column} * 1.2;

            $query->whereBetween($column, [$minPrice, $maxPrice]);
        }

        return $query->take(10)->get();
    }

    public function getUser(int $id)
    {
        return $this->model::select('id', 'title', 'price', 'location', 'views', 'images')
            ->where('id_user', $id)
            ->get();
    }

    public function getUserQueue(int $id): Collection
    {
        return $this->modelQueue::select('id', 'title', 'price', 'location', 'images', 'status_aproved')
            ->where('id_user', $id)
            ->get();
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

    /**
     * Inicio gestion de imagenes
     */

    public function uploadImages(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|file|image',
                'id' => 'required|integer',
                'index' => 'required|string'
            ]);

            $image = $request->file('image');
            $directoryPath = "img/postsqueue/{$this->directory}/{$request->id}";

            $fileName = Str::slug($request->index) . '.' . $image->extension();

            Storage::disk('public')->putFileAs(
                $directoryPath,
                $image,
                $fileName
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImages(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|min:1',
                'imageNames' => 'required|string'
            ]);

            $directoryPath = "img/postsqueue/{$this->directory}/{$validated['id']}";
            $deleted = false;

            collect(['jpg', 'jpeg', 'png', 'webp'])
                ->map(fn($ext) => "{$directoryPath}/{$validated['imageNames']}.{$ext}")
                ->each(function ($filePath) use (&$deleted) {
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                        $deleted = true;
                    }
                });

            if ($deleted) {
                $this->renameImages($directoryPath);
                $model = $this->modelQueue::findOrFail($validated['id']);
                $model->update(['images' => max(0, $model->images - 1)]);
            }

            return response()->json(['success' => true]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting images: ' . $e->getMessage()
            ], 500);
        }
    }

    private function renameImages(string $directoryPath): void
    {
        $files = collect(Storage::disk('public')->files($directoryPath))
            ->sortBy([
                fn($file) => (int) pathinfo($file, PATHINFO_FILENAME),
                SORT_NUMERIC
            ]);

        $tempFiles = $files->mapWithKeys(function ($file) use ($directoryPath) {
            $tempName = "temp_{$file}";
            Storage::disk('public')->move($file, "{$directoryPath}/{$tempName}");
            return [$file => $tempName];
        });

        $tempFiles->each(function ($tempName, $originalFile) use ($directoryPath) {
            $index = (int) pathinfo($originalFile, PATHINFO_FILENAME);
            $extension = pathinfo($originalFile, PATHINFO_EXTENSION);
            $newName = ($index) . ".{$extension}";

            Storage::disk('public')->move(
                "{$directoryPath}/{$tempName}",
                "{$directoryPath}/{$newName}"
            );
        });
    }

    protected function processImageOrdering(Request $request, int $terrainId): void
    {
        if (!$request->has('orderArray') || !is_array($request->orderArray)) {
            throw new InvalidArgumentException('orderArray must be an array');
        }

        $path = storage_path("app/public/img/postsqueue/{$this->directory}/{$terrainId}");
        $tempFiles = [];

        try {
            foreach ($request->orderArray as $originalPosition) {
                $source = "{$path}/{$originalPosition}.webp";
                $tempName = "{$originalPosition}temp.webp";

                if (File::exists($source)) {
                    File::move($source, "{$path}/{$tempName}");
                    $tempFiles[$originalPosition] = $tempName;
                }
            }

            foreach ($request->orderArray as $newPosition => $originalPosition) {
                $tempName = $tempFiles[$originalPosition] ?? null;
                $target = "{$path}/" . ($newPosition + 1) . ".webp";

                if ($tempName && File::exists("{$path}/{$tempName}")) {
                    File::move("{$path}/{$tempName}", $target);
                }
            }
        } catch (Exception $e) {
            Log::error("Error reorganizando imágenes: {$e->getMessage()}");
            throw $e;
        }
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
