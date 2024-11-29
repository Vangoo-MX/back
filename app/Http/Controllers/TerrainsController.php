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

class TerrainsController extends Controller
{
    public function getAll()
    {
        $terrains = Terrains::all();
        return $terrains;
    }

    public function getTerrainsHightlights()
    {
        $highlights = TerrainsHighlights::orderBy('num_order', 'asc')->get();

        if (sizeof($highlights) > 0) {
            $highlightIds = $highlights->pluck('id_property')->toArray();
            $highlights = Terrains::whereIn('id', $highlightIds)->get();
        } else {
            $highlights = [];
        }

        return $highlights;
    }


    public function getTerrainsHightlightFromMunicipio($id)
    {
        $highlight = TerrainsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->get();

        if (sizeof($highlight) > 0) {
            $terrains = Terrains::select();
            foreach ($highlight as $value) {
                $terrains = $terrains->orwhere('id', $value->id_property);
            }
            $terrains = $terrains->get();
        } else {
            $terrains = [];
        }

        return $terrains;
    }

    public function deleteTerrainHightlight($id)
    {
        $h = TerrainsHighlights::find($id);

        if ($h) {
            $h->delete();
            return redirect('overview/terrains-highlights');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function addTerrainHightlight(Request $request)
    {
        try {
            $h = new TerrainsHighlights();
            $h->id_estado = 19;
            $h->id_municipio = $request->id_municipio;
            $h->id_property = $request->id_property;
            $h->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return redirect('overview/terrains-highlights');
    }

    public function orderTerrainHightlight(Request $request)
    {
        $terrainID = $request->id;

        $h = TerrainsHighlights::where('id_property', $terrainID)->first();

        if ($h) {
            $h->num_order = $request->num_order;
            $h->save();

            return redirect('overview/terrains-highlights');
        } else {
            return json_encode('error: entry for property with id ' . $terrainID . ' not found');
        }
    }


    public function getTerrainCard($id)
    {
        $terrains = Terrains::selectRaw('id,title,price,location,id_pais,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images')
            ->where('id', $id)
            ->get();

        return $terrains;
    }

    public function getMultiTerrainCard($array)
    {

        if (str_contains($array, '-')) {
            $list = explode('-', $array);
        } else {
            $list[] = $array;
        }

        $terrains = Terrains::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images');

        foreach ($list as $value) {
            $terrains = $terrains->orWhere('id', $value);
        }

        $terrains = $terrains->get();

        return $terrains;
    }

    public function getTerrainsImagesCards()
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->get();

        return $images;
    }

    public function getTerrainsImagesDetail($id)
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->where('id_property', $id)
            ->get();

        return $images;
    }

    public function getTerrain($id)
    {
        $terrains = Terrains::where('id', $id)
            ->get();

        return $terrains;
    }

    public function getTerrainQueueEP($id)
    {
        $terrainQueue = TerrainsQueue::where('id', $id)->get();
        return $terrainQueue;
    }

    public function getTerrainsRelated($id)
    {
        $terrains = Terrains::where('id', $id)
            ->get();
        $terrainsRelated = Terrains::where('id', '<>', $id)
            ->where('type', $terrains[0]->type)
            ->where('bathrooms', $terrains[0]->bathrooms)
            ->where('rooms', $terrains[0]->rooms)
            ->where('id_municipio', $terrains[0]->id_municipio)
            ->take(10)
            ->get();

        return $terrainsRelated;
    }

    public function getTerrainSearch($estado = "0", $municipio = "0", $colonia = "0", $type = "alltypes", $min = 0, $max = 0)
    {

        $search = Terrains::select();

        if ($estado != "0" && $estado != 0) {
            $search = $search->where('id_estado', $estado);
        }
        if ($municipio != "0" && $municipio != 0) {
            $search = $search->where('id_municipio', $municipio);
        }
        if ($colonia != "0" && $colonia != 0) {
            $search = $search->where('id_colonia', $colonia);
        }

        if ($min != 0 || $max != 0) {
            $search = $search->where(function ($query) use ($min, $max) {
                if ($max == 0) {
                    $query->where('price', '>=', $min);
                } else {
                    $query->whereBetween('price', [$min, $max]);
                }
            });
        }

        if ($type == "casa&dpto") {
            $search = $search->where('type', 'casa');
            $search = $search->orWhere('type', 'departamento');
        } elseif ($type == "casa&terreno") {
            $search = $search->where('type', 'casa');
            $search = $search->orWhere('type', 'terreno');
        } elseif ($type == "dpto&terreno") {
            $search = $search->where('type', 'terreno');
            $search = $search->orWhere('type', 'departamento');
        } elseif ($type == "alltypes") {
        } else {
            $search = $search->where('type', $type);
        }

        $search = $search->paginate(50);

        return $search;
    }

    public function rejectTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(array('status_aproved' => 2));
        return redirect()->route('admin.queueTerrains');
    }

    public function revisionTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(array('status_aproved' => 3));
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

        $files = File::allFiles($sourcePath);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            File::move($sourcePath . $filename, $destinationPath . $filename);
        }

        $terrainQueue->delete();

        return redirect()->route('admin.queueTerrains');
    }


    public function deleteTerrainQueue($id)
    {

        $terrainQueue = TerrainsQueue::findOrFail($id);
        $terrainQueue->delete();
        return redirect()->route('admin.queue');
    }

    public function deleteTerrain($id)
    {
        $highlight = TerrainsHighlights::where('id_property', $id)->first();

        if ($highlight) {
            $highlight->delete();
        }
        $terrain = Terrains::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/terrains/{$terrain->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $terrain->delete();
        return redirect()->route('admin.terrains');
    }

    public function deleteImage(Request $request, $terrainId, $imageId)
    {
        $imagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $imageId . '.jpg';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $terrain = Terrains::findOrFail($terrainId);
            $terrain->images -= 1;
            $terrain->save();

            for ($i = $imageId + 1; $i <= $terrain->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $i . '.jpg';
                $newImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . ($i - 1) . '.jpg';

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

        $terrain = Terrains::findOrFail($id);

        $terrain->status = 0;

        $terrain->save();
        return redirect()->route('admin.terrains');
    }

    public function activeTerrain($id)
    {

        $aparment = Terrains::findOrFail($id);

        $aparment->status = 1;

        $aparment->save();
        return redirect()->route('admin.terrains');
    }

    public function deleteTerrainEP($id)
    {

        $terrain = Terrains::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/terrains/{$terrain->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
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


        if (isset($request->propertyAreaConstruction)) {
            $terrain->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyAreaTerrain)) {
            $terrain->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyBathrooms)) {
            $terrain->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $terrain->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyType)) {
            $terrain->type = $request->propertyType;
        }
        if (isset($request->propertyDevType)) {
            $terrain->dev_type = $request->propertyDevType;
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
        if (isset($request->propertyAmenities)) {
            $terrain->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $terrain->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $terrain->price_maintenance = $request->propertyPriceMaintenance;
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
        $imageNames = $request->imageNames;
        $id = $request->id;
        $route = public_path("storage/img/postsqueue/terrains/{$id}/");
        $extensions = ['jpg', 'jpeg', 'png'];
        // $deletedCount = 0;

        foreach ($imageNames as $imageName) {
            foreach ($extensions as $extension) {
                $imagePath = $route . $imageName . '.' . $extension;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    // $deletedCount++;
                    break;
                }
            }
        }

        $this->renameImages($route);

        // if ($deletedCount > 0) {
        //     $terrain = TerrainsQueue::find($id);
        //     if ($terrain) {
        //         $terrain->images = max(0, $terrain->images - $deletedCount);
        //         $terrain->save();
        //     }
        // }

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

        if (isset($request->propertyAreaConstruction)) {
            $terrain->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyAreaTerrain)) {
            $terrain->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyBathrooms)) {
            $terrain->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $terrain->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyType)) {
            $terrain->type = $request->propertyType;
        }
        if (isset($request->propertyDevType)) {
            $terrain->dev_type = $request->propertyDevType;
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
        if (isset($request->propertyAmenities)) {
            $terrain->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $terrain->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $terrain->price_maintenance = $request->propertyPriceMaintenance;
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
        if (isset($request->status_aproved)) {
            $terrain->status_aproved = $request->status_aproved;
        }

        $terrain->save();

        return json_encode($terrain->id);
    }

    public function getUserTerrains($iduser)
    {
        $terrains = Terrains::selectRaw('id,title,price,location,views,images')
            ->where('id_user', $iduser)
            ->get();

        return $terrains;
    }

    public function getUserTerrainsQueue($iduser)
    {
        $terrainsQueue = TerrainsQueue::selectRaw('id,title,price,location,views,images,status_aproved')
            ->where('id_user', $iduser)
            ->get();

        return $terrainsQueue;
    }

    public function getTerrainsByMunicipio($id)
    {
        $terrains = Terrains::where('id_municipio', $id)->get();
        return response()->json($terrains);
    }

    public function updateTerrains(Request $request, Terrains $terrains)
    {
        $updates = [];
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($terrains->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;
        if ($request->hasFile('images')) {
            $numImages = $terrains->images + sizeof($request->file('images'));
        } else {
            $numImages = $terrains->images;
        }
        $terrains->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'type' => $request->type,
            'price' => $request->price,
            'price_maintenance' => $request->price_maintenance,
            'description' => $request->description,
            'rooms' => $request->rooms,
            'bathrooms' => $request->bathrooms,
            'parkings' => $request->parkings,
            'map' => $request->map,
            'area' => $request->area,
            'dev_type' => $request->dev_type,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'num_int' => $request->num_int,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'amenities' => $request->amenities,
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

                $nameimg = Str::slug($terrains->images + $index) . "." . $image->getClientOriginalExtension();
                $image->storeAs('public/img/posts/terrains/' . $terrains->id . '/', $nameimg);
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$order}.jpg")) {
                    rename($path . "/{$order}.jpg", $path . "/{$order}temp.jpg");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.jpg")) {
                    rename($path . "/{$key}temp.jpg", $path . "/{$order}.jpg");
                }
            }
        }

        return redirect()->route('admin.detailsTerrains', $terrains)->with('success', 'Propiedad actualizada correctamente');
    }
}
