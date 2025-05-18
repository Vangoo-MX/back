<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;

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
        $directory = storage_path("app/public/img/{$this->directory}/properties/{$estate->id}");

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
        $directory = storage_path("app/public/img/{$this->directory}/properties/{$estate->id}");
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

    protected function getPropertyLocation(Model $estate): string
    {
        return collect([
            $estate->colonia->nombre ?? null,
            $estate->municipio->nombre ?? null,
            $estate->estado->nombre ?? null
        ])->filter()->join(', ');
    }
}
