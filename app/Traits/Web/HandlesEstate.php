<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use App\Models\Estados;
use App\Models\Colonias;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
            if ($highlight = $this->highlightModel::where($this->idHighlight, $id)->first()) {
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

    public function deleteDevelopment($id)
    {
        try {
            if ($highlight = $this->highlightModel::where('id_development', $id)->first()) {
                $highlight->delete();
            }

            $estate = $this->model::findOrFail($id);

            $directoryPath = public_path("storage/img/posts/{$this->directory}/{$estate->id}");
            if (File::exists($directoryPath)) {
                File::deleteDirectory($directoryPath);
            }

            if ($this->apartmentModel::where('id_development', $id)->exists()) {
                $this->apartmentModel::where('id_development', $id)->delete();
            }

            $estate->delete();

            return response()->json([
                'success' => true,
                'message' => __('Desarrollo vertical eliminado exitosamente')
            ], 200);
        } catch (Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
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

    public function deactiveEstate($id)
    {
        $this->model::findOrFail($id)->update(['status' => 0]);
        return response()->json([
            'success' => true,
            'message' => __('Propiedad desactivada exitosamente')
        ], 200);
    }

    public function activeEstate($id)
    {
        $this->model::findOrFail($id)->update(['status' => 1]);
        return response()->json([
            'success' => true,
            'message' => __('Propiedad activada exitosamente')
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

    private function setApartmentImageCount(Model $apartment, Request $request, $key, string $imageFieldName): void
    {
        $field = $imageFieldName . '.' . $key;

        if ($request->hasFile($field)) {
            $files = $request->file($field);
            $apartment->image_plans = is_array($files) ? count($files) : 1;
        } else {
            $apartment->image_plans = 0;
        }
    }

    private function proccessApartmentImages(Request $request, Model $estate, $key, string $imageFieldName): void
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

        foreach ($images as $image) {
            $nameImg = Str::slug($key) . '.webp';
            $path = $directory . $nameImg;

            $this->processSingleImage($image, $directory, $path);
        }
    }

    protected function updateApartments(Request $request, Model $estate, string $existingKey = 'optionapp', string $newKey = 'option', string $imageFieldName = 'imageoption')
    {
        $apartmentModel = $this->apartmentModel;
        $this->processExistingApartments(
            $request,
            $estate,
            $apartmentModel,
            $existingKey,
            $imageFieldName
        );

        $startingIndex = 1;
        if ($request->has($existingKey)) {
            $startingIndex = count($request->input($existingKey)) + 1;
        }

        $this->processNewApartments(
            $request,
            $estate,
            $apartmentModel,
            $newKey,
            $imageFieldName,
            $startingIndex
        );
    }

    private function processExistingApartments(Request $request, Model $estate, string $apartmentModel, string $keyName, string $imageFieldName): void
    {
        if (!$request->filled($keyName)) {
            return;
        }

        $key = 1;

        foreach ($request->input($keyName) as $option) {
            if (empty($option['id'])) {
                continue;
            }

            $apartment = $apartmentModel::findOrFail($option['id']);
            $this->fillApartmentData($apartment, $option);
            $this->setApartmentImageCount($apartment, $request, $key, $imageFieldName);
            $apartment->save();

            $this->proccessApartmentImages($request, $estate, $key, $imageFieldName);

            $key++;
        }
    }

    private function processNewApartments(Request $request, Model $estate, string $apartmentModel, string $keyName, string $imageFieldName, int $startingIndex = 1): void
    {
        if (!$request->filled($keyName)) {
            return;
        }

        $optionKeys = array_keys($request->input($keyName));
        foreach ($optionKeys as $index) {
            $option = $request->input("{$keyName}.{$index}");
            $apartment = new $apartmentModel;
            $apartment->id_development = $estate->id;

            $this->fillApartmentData($apartment, $option);
            $this->setApartmentImageCount($apartment, $request, $index, $imageFieldName);
            $apartment->save();

            $this->proccessApartmentImages($request, $estate, $index, $imageFieldName);
        }
    }
}
