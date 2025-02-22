<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Terrains;
use App\Models\TerrainsQueue;
use App\Models\TerrainsHighlights;
use App\Models\Images;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class TerrainsController extends Controller
{
    public function getAll()
    {
        return Terrains::all();
    }

    public function getTerrainsHightlights()
    {
        $highlightIds = TerrainsHighlights::orderBy('num_order', 'asc')
            ->pluck();

        return $highlightIds->isNotEmpty()
            ? Terrains::whereIn('id', $highlightIds)->get()
            : collect();
    }


    public function getTerrainsHightlightFromMunicipio($id)
    {
        $highlightIds = TerrainsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_property');

        return $highlightIds->isNotEmpty()
            ? Terrains::whereIn('id', $highlightIds)->get()
            : collect();
    }

    public function deleteTerrainHightlight($id)
    {
        if (TerrainsHighlights::destroy($id)) {
            return redirect('overview/terrains-highlights');
        } else {
            return response()->json(['error' => 'Agenda entry not found'], 404);
        }
    }

    public function addTerrainHightlight(Request $request)
    {
        try {
            TerrainsHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_property' => $request->id_property,
            ]);

            return redirect('overview/properties-highlights');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderTerrainHightlight(Request $request)
    {
        $highlight = TerrainsHighlights::where('id_property', $request->id_property)->first();

        if ($highlight) {
            $highlight->update([
                'num_order' => $request->num_order,
            ]);

            return redirect('overview/terrains-highlights');
        }

        return response()->json(['error' => 'Entry for property with id ' . $request->id . ' not found'], 404);
    }


    public function getTerrainCard($id)
    {
        return Terrains::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'parkings',
            'area_terrain',
            'description',
            'services',
            'views',
            'images',
        ])->find($id);
    }

    public function getMultiTerrainCard($array)
    {
        $ids = str_contains($array, ',') ? explode(',', $array) : [$array];

        return Terrains::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'parkings',
            'area_terrain',
            'description',
            'services',
            'views',
            'images',
        ])->whereIn('id', $ids)->get();
    }

    public function getTerrainsImagesCards()
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card']
        ])->get();
    }

    public function getTerrainsImagesDetail($id)
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card'],
            ['id_property', '=', $id]
        ])->get();
    }

    public function getTerrain($id)
    {
        return Terrains::where('id', $id)->get();
    }

    public function getTerrainQueueEP($id)
    {
        return TerrainsQueue::where('id', $id)->get();
    }

    public function getTerrainsRelated($id)
    {
        $terrains = Terrains::where('id', $id)
            ->first();

        if (!$terrains) {
            return collect();
        }

        return Terrains::where('id', '<>', $id)
            ->where('id_municipio', $terrains[0]->id_municipio)
            ->limit(10)
            ->get();
    }

    public function getTerrainSearch($estado = "0", $municipio = "0", $colonia = "0", $type = "alltypes", $min = 0, $max = 0)
    {

        $search = Terrains::query();

        if ($estado != "0") {
            $search->where('id_estado', $estado);
        }
        if ($municipio != "0") {
            $search->where('id_municipio', $municipio);
        }
        if ($colonia != "0") {
            $search->where('id_colonia', $colonia);
        }

        if ($min != 0 || $max != 0) {
            $search->where(function ($query) use ($min, $max) {
                if ($max == 0) {
                    $query->where('price', '>=', $min);
                } else {
                    $query->whereBetween('price', [$min, $max]);
                }
            });
        }

        if ($type !== "alltypes") {
            $typeMapping = [
                "casa&dpto" => ['casa', 'departamento'],
                "casa&terreno" => ['casa', 'terreno'],
                "dpto&terreno" => ['departamento', 'terreno'],
            ];

            if (array_key_exists($type, $typeMapping)) {
                $search->whereIn('type', $typeMapping[$type]);
            } else {
                $search->where('type', $type);
            }
        }

        return $search->paginate(50);
    }

    public function rejectTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(['status_aproved' => 2]);
        return redirect()->route('admin.queueTerrains');
    }

    public function revisionTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(['status_aproved' => 3]);
        return redirect()->route('admin.queueTerrains');
    }

    public function aprovedTerrainsQueue(Request $request)
    {
        $terrainQueue = TerrainsQueue::findOrFail($request->id);
        $terrainData = Arr::except($terrainQueue->toArray(), ['id']);
        $newTerrain = Terrains::create($terrainData);
        $sourcePath = public_path("storage/img/postsqueue/terrains/" . $request->id . "/");
        $destinationPath = public_path("storage/img/posts/terrains/" . $newTerrain->id . "/");

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        foreach (File::allFiles($sourcePath) as $file) {
            File::move($file->getRealPath(), $destinationPath . $file->getFilename());
        }

        File::deleteDirectory($sourcePath, true);

        $terrainQueue->delete();

        return redirect()->route('admin.queueTerrains');
    }


    public function deleteTerrainQueue($id)
    {

        TerrainsQueue::findOrFail($id)->delete();
        return redirect()->route('admin.queue');
    }

    public function deleteTerrainQueueEP($id)
    {
        $terrainQueue = TerrainsQueue::findOrFail($id);
        $directoryPath = public_path("storage/img/postsqueue/terrains/{$terrainQueue->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $terrainQueue->delete();
        return json_encode("success");
    }

    public function deleteTerrain($id)
    {
        if ($highlight = TerrainsHighlights::where('id_property', $id)->first()) {
            $highlight->delete();
        }

        $terrain = Terrains::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/terrains/{$terrain->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $terrain->delete();
        return redirect()->route('admin.terrains');
    }

    public function deleteImage(Request $request, $terrainId, $imageId)
    {
        $imagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $imageId . '.webp';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $terrain = Terrains::findOrFail($terrainId);
            $terrain->images -= 1;
            $terrain->save();

            for ($i = $imageId + 1; $i <= $terrain->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $i . '.webp';
                $newImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . ($i - 1) . '.webp';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }
    }

    public function deactiveTerrain($id)
    {
        Terrains::findOrFail($id)->update(['status' => 0]);
        return redirect()->route('admin.terrains');
    }

    public function activeTerrain($id)
    {
        Terrains::findOrFail($id)->update(['status' => 1]);
        return redirect()->route('admin.terrains');
    }

    public function deleteTerrainEP($id)
    {
        $terrain = Terrains::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/terrains/{$terrain->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $terrain->delete();
        return json_encode("success");
    }

    public function postTerrainsQueue(Request $request)
    {

        $terrain = new TerrainsQueue();
        $terrain->title = $request->propertyTitle;
        $terrain->price = $request->propertySellPrice;
        if (isset($request->propertyIntNumber)) {
            $terrain->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $terrain->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $terrain->street = $request->propertyStreet;
        }

        $terrain->id_colonia = $request->propertyColonia;

        $terrain->id_municipio = $request->propertyMunicipio;

        $terrain->id_estado = $request->propertyEstado;

        $terrain->id_pais = 1;
        if (isset($request->propertyCP)) {
            $terrain->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $terrain->location = $colonia . ', ' . $municipio . ', ' . $estado;

        if (isset($request->propertyAreaTerrain)) {
            $terrain->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyParkings)) {
            $terrain->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $terrain->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $terrain->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $terrain->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $terrain->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $terrain->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyOperationType)) {
            $terrain->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $terrain->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $terrain->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $terrain->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyServices)) {
            $terrain->services = $request->propertyServices;
        }
        if (isset($request->propertyExactLocation)) {
            $terrain->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }
        if (isset($request->number_images)) {
            $terrain->images = $request->number_images;
        }
        if (isset($request->id_user)) {
            $terrain->id_user = $request->id_user;
        }
        $terrain->views = 0;

        $terrain->save();

        return json_encode($terrain->id);
    }

    public function imagesTerrainsQueue(Request $request)
    {

        if ($request->hasFile('image')) {
            $imagen = $request->file('image');
            $directory = 'public/img/postsqueue/terrains/' . $request->id . '/';
            $nameimg = Str::slug($request->index) . "." . $imagen->getClientOriginalExtension();
            $path = storage_path('app/public/img/postsqueue/terrains/' . $request->id . '/');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            chmod($path, 0755);
            $imagen->storeAs($directory, $nameimg);
        }

        return json_encode('success');
    }

    public function deleteImagesTerrainsQueue(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json(['error' => 'ID inválido'], 400);
        }
        $imageNames = $request->imageNames;
        $route = public_path("storage/img/postsqueue/terrains/{$id}/");
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $deletedCount = 0;

        foreach ($extensions as $extension) {
            $imagePath = $route . $imageNames . '.' . $extension;
            if (file_exists($imagePath)) {
                unlink($imagePath);
                $deletedCount++;
                break;
            }
        }

        $this->renameImages($route);

        if ($deletedCount > 0) {
            $terrain = TerrainsQueue::findOrFail($id);
            if ($terrain) {
                $terrain->images = max(0, $terrain->images - $deletedCount);
                $terrain->save();
            }
        }

        return response()->json("success");
    }

    private function renameImages($route)
    {
        $images = collect(File::files($route))
            ->sortBy(function ($file) {
                return $file->getFilename();
            });

        $images->each(function ($file, $index) use ($route) {
            $newName = ($index + 1) . '.' . $file->getExtension();
            File::move($file->getPathname(), $route . $newName);
        });
    }

    public function updateTerrainsQueue(Request $request)
    {

        $terrain = TerrainsQueue::findOrFail($request->id);

        $terrain->title = $request->propertyTitle;
        $terrain->price = $request->propertySellPrice;

        if (isset($request->propertyIntNumber)) {
            $terrain->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $terrain->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $terrain->street = $request->propertyStreet;
        }

        $terrain->id_colonia = $request->propertyColonia;

        $terrain->id_municipio = $request->propertyMunicipio;

        $terrain->id_estado = $request->propertyEstado;

        $terrain->id_pais = 1;
        if (isset($request->propertyCP)) {
            $terrain->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $terrain->location = $colonia . ', ' . $municipio . ', ' . $estado;

        if (isset($request->propertyAreaTerrain)) {
            $terrain->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyParkings)) {
            $terrain->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $terrain->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $terrain->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $terrain->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $terrain->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $terrain->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyOperationType)) {
            $terrain->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $terrain->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $terrain->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $terrain->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyServices)) {
            $terrain->services = $request->propertyServices;
        }
        if (isset($request->propertyExactLocation)) {
            $terrain->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }

        if (isset($request->number_images)) {
            $terrain->images += $request->number_images;
        }
        if (isset($request->status_aproved)) {
            $terrain->status_aproved = $request->status_aproved;
        }

        if ($request->has('orderArray') && is_array($request->orderArray)) {
            $orderArray = $request->orderArray;

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/terrains/' . $request->id);
                $tempFile = $path . "/{$order}.webp";

                if (file_exists($tempFile)) {
                    rename($tempFile, $path . "/{$order}temp.webp");
                } else {
                    Log::warning("File not found during temp rename: {$tempFile}");
                }
            }

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/terrains/' . $request->id);
                $tempFile = $path . "/{$order}temp.webp";
                $finalFile = $path . "/" . ($index + 1) . ".webp";

                if (file_exists($tempFile)) {
                    rename($tempFile, $finalFile);
                } else {
                    Log::warning("Temp file not found during final rename: {$tempFile}");
                }
            }
        } else {
            return response()->json(['error' => 'orderArray must be an array'], 400);
        }

        $terrain->save();

        return json_encode($terrain->id);
    }

    public function getUserTerrains($iduser)
    {
        return Terrains::select('id', 'title', 'price', 'location', 'views', 'images')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getUserTerrainsQueue($iduser)
    {
        return TerrainsQueue::select('id', 'title', 'price', 'location', 'images', 'status_aproved')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getTerrainsByMunicipio($id)
    {
        return response()->json(Terrains::where('id_municipio', $id)->get());
    }

    public function updateTerrains(Request $request, Terrains $terrains)
    {
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($terrains->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;

        $images = $terrains->images;

        if ($request->hasFile('images')) {
            $numImages = $images + sizeof($request->file('images'));
        } else {
            $numImages = $images;
        }

        $terrains->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'price' => $request->price,
            'description' => $request->description,
            'parkings' => $request->parkings,
            'map' => $request->map,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'num_int' => $request->num_int,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'services' => $request->services,
            'sell_type' => $request->sell_type,
            'share_conditions' => $request->share_conditions,
            'antiquity' => $request->antiquity,
            'location' => $location,
            'images' => $numImages,
            'area_terrain' => $request->area_terrain,
            'price_m2' => $request->price_m2,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/terrains/' . $request->id . '/');
                $imageName = Str::slug($images + $index + 1) . '.webp';

                if ($image->getClientOriginalExtension() === 'webp') {
                    $image->move($path, $imageName);
                } else {
                    $imageWebp = Image::make($image->getRealPath())
                        ->encode('webp', 90);

                    $imageWebp->save($path . $imageName);
                }
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$order}.webp")) {
                    rename($path . "/{$order}.webp", $path . "/{$order}temp.webp");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.webp")) {
                    rename($path . "/{$key}temp.webp", $path . "/{$order}.webp");
                }
            }
        }

        return redirect()->route('admin.detailsTerrains', $terrains)->with('success', 'Propiedad actualizada correctamente');
    }
}
