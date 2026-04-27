<?php

namespace App\Traits\Utility;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesImage
{
    protected function handleImageProcessing(Request $request, Model $estate): void
    {
        if ($request->hasFile('images')) {
            $this->processNewImages($request->file('images'), $estate);
        }
    }

    protected function processNewImages(array $images, Model $estate): void
    {
        $directory = storage_path("app/public/img/{$this->basePath}/{$this->directory}/{$estate->id}");

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        chmod($directory, 0755);

        $existingImages = $estate->images ?? [];
        $newImages = [];

        foreach ($images as $image) {
            $filename = $this->generateFilename($image);
            $this->processSingleImage($image, $directory, $filename);
            $newImages[] = $filename;
        }

        $estate->images = array_merge($existingImages, $newImages);
        $estate->save();
    }

    protected function generateFilename(UploadedFile $image): string
    {
        return Str::uuid() . '.webp';
    }

    private function processSingleImage(UploadedFile $image, string $directory, string $filename): void
    {
        $fullPath = $directory . '/' . $filename;
        if (strtolower($image->extension()) === 'webp') {
            $image->move($directory, $filename);
        } else {
            Image::make($image)
                ->encode('webp', 80)
                ->save($fullPath);
        }
    }

    protected function reorderEstateImages(Request $request, Model $estate)
    {
        $request->validate([
            'new_order' => 'required|array',
            'new_order.*' => 'string'
        ]);

        $currentImages = $estate->images ?? [];

        if (is_string($currentImages)) {
            $currentImages = json_decode($currentImages, true) ?? [];
        }


        foreach ($request->new_order as $filename) {
            if (!in_array($filename, $currentImages)) {
                return response()->json([
                    'error' => 'El archivo ' . $filename . ' no pertenece a este inmueble'
                ], 422);
            }
        }

        try {
            $estate->update(['images' => $request->new_order]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function destroyImage($estate, string $filename)
    {
        if (!in_array($filename, $estate->images ?? [], true)) {
            return response()->json([
                'success' => false,
                'error' => 'La imagen no existe en este registro'
            ], 404);
        }

        $path = "public/img/{$this->basePath}/{$this->directory}/{$estate->id}/{$filename}";

        if (Storage::exists($path)) {
            Storage::delete($path);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Archivo no encontrado en el servidor'
            ], 404);
        }

        $updatedImages = array_values(array_filter($estate->images, function ($item) use ($filename) {
            return $item !== $filename;
        }));

        $estate->images = $updatedImages;
        $estate->save();

        return response()->json(['success' => true]);
    }
}
