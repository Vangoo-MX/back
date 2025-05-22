<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

trait HandlesEstate
{
    public function indexEstate($viewEstate)
    {
        $estates = $this->model::all();

        return view($viewEstate, compact('estates'));
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

    protected function handleImageProcessing(Request $request, Model $estate): void
    {
        if ($request->hasFile('images')) {
            $this->processNewImages($request->file('images'), $estate);
        }

        if ($request->orderimg && is_array($request->orderimg)) {
            $this->reorderImages($request->orderimg, $estate);
        }
    }

    protected function processNewImages(array $images, Model $estate): void
    {
        $directory = storage_path("app/public/img/posts/{$this->directory}/{$estate->id}");

        File::ensureDirectoryExists($directory);

        foreach ($images as $index => $image) {
            $imageName = Str::slug($estate->images + $index + 1) . '.webp';
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

    protected function getLocation(Model $estate): string
    {
        return collect([
            $estate->colonia->nombre ?? null,
            $estate->municipio->nombre ?? null,
            $estate->estado->nombre ?? null
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
}
