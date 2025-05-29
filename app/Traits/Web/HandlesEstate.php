<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use App\Models\Estados;
use App\Models\Colonias;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesEstate
{
    public function indexEstate($viewEstate)
    {
        $estates = $this->model::all();

        return view($viewEstate, compact('estates'));
    }

    public function createEstate($viewEstate)
    {
        $municipios = Municipios::where('id_estado', 19)->get();

        return view($viewEstate, compact('municipios'));
    }

    public function showEstate($id, $viewEstate)
    {
        $estate = $this->model::findOrFail($id);

        return view($viewEstate, compact('estate'));
    }

    public function editEstate($id, $viewEstate)
    {
        $estate = $this->model::findOrFail($id);
        $municipios = Municipios::where('id_estado', 19)->get();

        return view($viewEstate, compact('estate', 'municipios'));
    }

    public function editDevelopment($id, $viewEstate)
    {
        $estate = $this->model::findOrFail($id);
        $municipios = Municipios::where('id_estado', 19)->get();
        $apartments = $this->apartmentModel::where('id_development', $id)
            ->get();

        return view($viewEstate, compact('estate', 'municipios', 'apartments'));
    }

    public function deleteEstate($id)
    {
        try {
            if ($highlight = $this->highlightModel::where('id_property', $id)->first()) {
                $highlight->delete();
            }

            $estate = $this->model::findOrFail($id);

            $directoryPath = public_path("storage/img/posts/{$this->directory}/{$estate->id}");

            if (File::exists($directoryPath)) {
                File::deleteDirectory($directoryPath);
            }

            $estate->delete();

            return response()->json([
                'success' => true,
                'message' => __('Propiedad eliminada exitosamente')
            ], 200);
        } catch (Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    protected function handleImageProcessing(Request $request, Model $estate, bool $isUpdate = true): void
    {
        if ($request->hasFile('images')) {
            $this->processNewImages($request->file('images'), $estate, $isUpdate);
        }

        if ($request->orderimg && is_array($request->orderimg)) {
            $this->reorderImages($request->orderimg, $estate);
        }
    }

    protected function processNewImages(array $images, Model $estate, bool $isUpdate): void
    {
        $directory = storage_path("app/public/img/posts/{$this->directory}/{$estate->id}");

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        chmod($directory, 0755);

        $startingIndex = $isUpdate ? $estate->images : 0;

        foreach ($images as $index => $image) {
            $imageName = Str::slug($startingIndex + $index + 1) . '.webp';
            $path = "{$directory}/{$imageName}";

            $this->processSingleImage($image, $directory, $path);
        }
    }

    private function processSingleImage(UploadedFile $image, string $directory, string $path): void
    {
        if (strtolower($image->extension()) === 'webp') {
            $image->move($directory, basename($path));
        } else {
            Image::make($image->getRealPath())
                ->encode('webp', 90)
                ->save($path);
        }
    }

    protected function reorderImages(array $newOrder, Model $estate): void
    {
        $directory = storage_path("app/public/img/posts/{$this->directory}/{$estate->id}");
        $tempPrefix = 'reorder_temp_';

        foreach ($newOrder as $newPosition => $originalPosition) {
            $originalFile = "{$directory}/{$originalPosition}.webp";
            $tempFile = "{$directory}/{$tempPrefix}{$newPosition}.webp";

            if (File::exists($originalFile)) {
                File::move($originalFile, $tempFile);
            }
        }

        foreach ($newOrder as $newPosition => $originalPosition) {
            $tempFile = "{$directory}/{$tempPrefix}{$newPosition}.webp";
            $newFile = "{$directory}/{$newPosition}.webp";

            if (File::exists($tempFile)) {
                File::move($tempFile, $newFile);
            }
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

    protected function deleteImage(Model $estate, int $imageId): bool
    {
        $imagePath = "public/img/posts/{$this->directory}/{$estate->id}/{$imageId}.webp";

        if (!Storage::exists($imagePath)) {
            return false;
        }

        Storage::delete($imagePath);

        $this->decrementImageCount($estate);
        $this->reorderRemainingImages($estate, $imageId);

        return true;
    }

    private function decrementImageCount(Model $estate): void
    {
        $estate->images = max(0, $estate->images - 1);
        $estate->save();
    }

    private function reorderRemainingImages(Model $estate, int $deletedImageId): void
    {
        $directory = "public/img/posts/{$this->directory}/{$estate->id}";

        for ($i = $deletedImageId + 1; $i <= $estate->images + 1; $i++) {
            $oldPath = "{$directory}/{$i}.webp";
            $newPath = "{$directory}/" . ($i - 1) . ".webp";

            if (Storage::exists($oldPath)) {
                Storage::move($oldPath, $newPath);
            }
        }
    }

    public function deactiveEstate($id)
    {
        $this->model::findOrFail($id)->update(['status' => 0]);
        return response()->json([
            'success' => true,
            'message' => __('Propiedad eliminada exitosamente')
        ], 200);
    }

    public function activeEstate($id)
    {
        $this->model::findOrFail($id)->update(['status' => 1]);
        return response()->json([
            'success' => true,
            'message' => __('Propiedad eliminada exitosamente')
        ], 200);
    }

    protected function processApartments(Request $request, Model $estate,  string $imageFieldName = 'imageoption'): void
    {
        if (!$request->filled('option') || !is_array($request->option)) {
            return;
        }

        $key = 1;
        foreach ($request->option as $option) {
            $apartment = new $this->apartmentModel;
            $apartment->id_development = $estate->id;

            $this->fillApartmentData($apartment, $option);
            $this->setApartmentImageCount($apartment, $request, $key, $imageFieldName);
            $apartment->save();

            $this->proccessApartmentImages($request, $estate, $key, $imageFieldName);
            $key++;
        }
    }

    private function fillApartmentData(Model $apartment, array $optionData): void
    {
        $apartment->title = $optionData['title'];
        $apartment->price = $optionData['price'];
        $apartment->rooms = $optionData['rooms'];
        $apartment->bathrooms = $optionData['bathrooms'];
        $apartment->parkings = $optionData['parkings'];
        $apartment->area = $optionData['area'];
        $apartment->num_available = $optionData['num_available'] ?? 0;
    }

    private function setApartmentImageCount(Model $apartment, Request $request, int $key, string $imageFieldName): void
    {
        $field = $imageFieldName . '.' . $key;

        if ($request->hasFile($field)) {
            $files = $request->file($field);
            $apartment->image_plans = is_array($files) ? count($files) : 1;
        } else {
            $apartment->image_plans = 0;
        }
    }

    private function proccessApartmentImages(Request $request, Model $estate, int $key, string $imageFieldName): void
    {
        $field = $imageFieldName . '.' . $key;

        if (!$request->hasFile($field)) {
            return;
        }

        $directory = storage_path("app/public/img/posts/{$this->directory}/{$estate->id}/plans/");

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        chmod($directory, 0755);

        $images = $request->file($field);

        $images = is_array($images) ? $images : [$images];

        $index = 1;

        foreach ($images as $image) {
            $nameImg = Str::slug($key) . '.webp';
            $path = $directory . $nameImg;

            $this->processSingleImage($image, $directory, $path);
            $index++;
        }
    }
}
