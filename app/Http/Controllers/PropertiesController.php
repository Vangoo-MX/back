<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use App\Models\Images;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;

class PropertiesController extends Controller
{
    public function getAll()
    {
        $properties = Properties::all();
        //return $users;
        return $properties;
    }

    public function getPropertiesHightlights()
    {
        $highlights = PropertiesHighlights::orderBy('num_order', 'asc')->get();

        if (sizeof($highlights) > 0) {
            $highlightIds = $highlights->pluck('id_property')->toArray();
            $highlights = Properties::whereIn('id', $highlightIds)->get();
        } else {
            $highlights = [];
        }

        return $highlights;
    }


    public function getPropertiesHightlightFromMunicipio($id)
    {
        $highlight = PropertiesHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->get();

        if (sizeof($highlight) > 0) {
            $properties = Properties::select();
            foreach ($highlight as $value) {
                $properties = $properties->orwhere('id', $value->id_property);
            }
            $properties = $properties->get();
        } else {
            $properties = [];
        }

        return $properties;
    }

    public function deletePropertyHightlight($id)
    {
        $h = PropertiesHighlights::find($id);

        if ($h) {
            $h->delete();
            return redirect('overview/properties-highlights');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function addPropertyHightlight(Request $request)
    {
        try {
            $h = new PropertiesHighlights();
            $h->id_estado = 19;
            $h->id_municipio = $request->id_municipio;
            $h->id_property = $request->id_property;
            $h->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return redirect('overview/properties-highlights');
    }

    public function orderPropertyHightlight(Request $request)
    {
        $idProperty = $request->id;

        $h = PropertiesHighlights::where('id_property', $idProperty)->first();

        if ($h) {
            $h->num_order = $request->num_order;
            $h->save();

            return redirect('overview/properties-highlights');
        } else {
            return json_encode('error: entry for property with id ' . $idProperty . ' not found');
        }
    }


    public function getPropertyCard($id)
    {
        $properties = Properties::selectRaw('id,title,price,location,id_pais,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images')
            ->where('id', $id)
            ->get();

        return $properties;
    }

    public function getMultiPropertyCard($array)
    {

        if (str_contains($array, '-')) {
            $list = explode('-', $array);
        } else {
            $list[] = $array;
        }

        $properties = Properties::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images');

        foreach ($list as $value) {
            $properties = $properties->orWhere('id', $value);
        }

        $properties = $properties->get();

        return $properties;
    }

    public function getPropertiesImagesCards()
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->get();

        return $images;
    }

    public function getPropertiesImagesDetail($id)
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->where('id_property', $id)
            ->get();

        return $images;
    }

    public function getProperty($id)
    {
        $properties = Properties::where('id', $id)
            ->get();

        return $properties;
    }

    public function getPropertyQueueEP($id)
    {
        $propertyQueue = PropertiesQueue::where('id', $id)->get();
        return $propertyQueue;
    }

    public function getPropertyRelated($id)
    {
        $properties = Properties::where('id', $id)
            ->get();
        $propertiesRelated = Properties::where('id', '<>', $id)
            ->where('type', $properties[0]->type)
            ->where('bathrooms', $properties[0]->bathrooms)
            ->where('rooms', $properties[0]->rooms)
            ->where('id_municipio', $properties[0]->id_municipio)
            ->take(10)
            ->get();

        return $propertiesRelated;
    }

    public function getPropertySearch($estado = "0", $municipio = "0", $colonia = "0", $type = "alltypes", $min = 0, $max = 0)
    {

        $search = Properties::select();

        if ($estado != "0" && $estado != 0) {
            $search = $search->where('id_estado', $estado);
        }
        if ($municipio != "0" && $municipio != 0) {
            $search = $search->where('id_municipio', $municipio);
        }
        if ($colonia != "0" && $colonia != 0) {
            $search = $search->where('id_colonia', $colonia);
        }
        if ($max == 0) {
            $search = $search->where('price', '>', $min);
        } else {
            $search = $search->where('price', '>', $min);
            $search = $search->where('price', '<', $max);
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

    public function rejectPropertyQueue($id)
    {
        PropertiesQueue::where('id', $id)->update(array('status_aproved' => 2));
        return redirect()->route('admin.queue');
    }

    public function revisionPropertyQueue($id)
    {
        PropertiesQueue::where('id', $id)->update(array('status_aproved' => 3));
        return redirect()->route('admin.queue');
    }

    public function aprovedPropertyQueue(Request $request)
    {
        $propertyQueue = PropertiesQueue::findOrFail($request->id);
        $propertyData = Arr::except($propertyQueue->toArray(), ['id']);
        $newProperty = Properties::create($propertyData);

        $sourcePath = public_path("storage/img/postsqueue/properties/" . $request->id . "/");
        $destinationPath = public_path("storage/img/posts/properties/" . $newProperty->id . "/");

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        $files = File::allFiles($sourcePath);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            File::move($sourcePath . $filename, $destinationPath . $filename);
        }

        $propertyQueue->delete();

        return redirect()->route('admin.queue');
    }


    public function deletePropertyQueue($id)
    {

        $propertyQueue = PropertiesQueue::findOrFail($id);
        $propertyQueue->delete();
        return redirect()->route('admin.queue');
    }

    public function deletePropertyQueueEP($id)
    {

        $propertyQueue = PropertiesQueue::findOrFail($id);
        $propertyQueue->delete();
        return json_encode("success");
    }

    public function deleteProperty($id)
    {

        $property = Properties::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/properties/{$property->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $property->delete();
        return redirect()->route('admin.properties');
    }

    public function deactiveProperty($id)
    {

        $property = Properties::findOrFail($id);

        $property->status = 0;

        $property->save();
        return redirect()->route('admin.properties');
    }

    public function deletePropertyEP($id)
    {

        $property = Properties::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/properties/{$property->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $property->delete();
        return json_encode("success");
    }

    public function getPropertyQueue($id)
    {

        $propertyQueue = PropertiesQueue::where('id', $id)->get();
        return view('admin.propertyqueue', compact('propertyQueue'));
    }

    public function postPropertiesQueue(Request $request)
    {

        $property = new PropertiesQueue();
        $property->title = $request->propertyTitle;
        $property->price = $request->propertySellPrice;
        if (isset($request->propertyIntNumber)) {
            $property->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $property->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $property->street = $request->propertyStreet;
        }

        $property->id_colonia = $request->propertyColonia;

        $property->id_municipio = $request->propertyMunicipio;

        $property->id_estado = $request->propertyEstado;

        $property->id_pais = 1;
        if (isset($request->propertyCP)) {
            $property->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $property->location = $colonia . ', ' . $municipio . ', ' . $estado;


        if (isset($request->propertyAreaConstruction)) {
            $property->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyAreaTerrain)) {
            $property->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyBathrooms)) {
            $property->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $property->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyType)) {
            $property->type = $request->propertyType;
        }
        if (isset($request->propertyDevType)) {
            $property->dev_type = $request->propertyDevType;
        }
        if (isset($request->propertyParkings)) {
            $property->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $property->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $property->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $property->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $property->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $property->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyAmenities)) {
            $property->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $property->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $property->price_maintenance = $request->propertyPriceMaintenance;
        }
        if (isset($request->propertyOperationType)) {
            $property->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $property->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $property->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $property->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyServices)) {
            $property->services = $request->propertyServices;
        }
        if (isset($request->propertyExactLocation)) {
            $property->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }
        if (isset($request->number_images)) {
            $property->images = $request->number_images;
        }
        if (isset($request->id_user)) {
            $property->id_user = $request->id_user;
        }
        $property->views = 0;

        $property->save();

        return json_encode($property->id);
    }
    /*
    public function imagesPropertyQueue(Request $request) {
        //Guardar imagenes
        if ($request->file('image') != [] && $request->file('image') != null) {

            // Iterar a través de todas las imágenes proporcionadas en la solicitud
            foreach ($request->file('image') as $key => $image) {
                // Generar un nombre de archivo único para la imagen
                $nameimg = Str::slug($request->id)."_".$key.".".$image->getClientOriginalExtension();

                $route = public_path("img/postsqueue/properties/");
                $image->move($route, $nameimg);
                //$property->image_url = $nameimg;
                //array_push($routes, $nameimg);
            }
            return json_encode('success');
        }else{
            return response()->json([
            'error' => 'No se ha proporcionado ninguna imagen',
            "request" => $request
            ], 400);
        }
    }
*/
    public function imagesPropertyQueue(Request $request)
    {

        if ($request->hasFile('image')) {
            $imagen = $request->file('image');
            $nameimg = Str::slug($request->index) . "." . $imagen->getClientOriginalExtension();
            $imagen->storeAs('public/img/postsqueue/properties/' . $request->id . '/', $nameimg);
        }

        return json_encode('success');
    }

    public function deleteImagesPropertyQueue(Request $request)
    {
        $imageNames = $request->imageNames;
        $id = $request->id;

        $route = public_path("storage/img/postsqueue/properties/{$id}/");

        foreach ($imageNames as $imageName) {
            $imagePath = $route . $imageName . 'jpg';
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->renameImages($route);

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
            $file->move($route, $newName);
        });
    }


    public function updatePropertiesQueue(Request $request)
    {

        $property = PropertiesQueue::findOrFail($request->id);

        $property->title = $request->propertyTitle;
        $property->price = $request->propertySellPrice;

        if (isset($request->propertyIntNumber)) {
            $property->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $property->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $property->street = $request->propertyStreet;
        }

        $property->id_colonia = $request->propertyColonia;

        $property->id_municipio = $request->propertyMunicipio;

        $property->id_estado = $request->propertyEstado;

        $property->id_pais = 1;
        if (isset($request->propertyCP)) {
            $property->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $property->location = $colonia . ', ' . $municipio . ', ' . $estado;

        if (isset($request->propertyAreaConstruction)) {
            $property->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyAreaTerrain)) {
            $property->area_terrain = $request->propertyAreaTerrain;
        }
        if (isset($request->propertyBathrooms)) {
            $property->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $property->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyType)) {
            $property->type = $request->propertyType;
        }
        if (isset($request->propertyDevType)) {
            $property->dev_type = $request->propertyDevType;
        }
        if (isset($request->propertyParkings)) {
            $property->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $property->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $property->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $property->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $property->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $property->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyAmenities)) {
            $property->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $property->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $property->price_maintenance = $request->propertyPriceMaintenance;
        }
        if (isset($request->propertyOperationType)) {
            $property->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $property->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $property->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $property->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyServices)) {
            $property->services = $request->propertyServices;
        }
        if (isset($request->propertyExactLocation)) {
            $property->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }
        if (isset($request->number_images)) {
            $property->images = $request->number_images;
        }
        if (isset($request->status_aproved)) {
            $property->status_aproved = $request->status_aproved;
        }

        $property->save();

        return json_encode($property->id);
    }

    public function getUserProperties($iduser)
    {
        $properties = Properties::selectRaw('id,title,price,location,views,images')
            ->where('id_user', $iduser)
            ->get();

        return $properties;
    }

    public function getUserPropertiesQueue($iduser)
    {
        $propertiesQueue = PropertiesQueue::selectRaw('id,title,price,location,views,images,status_aproved')
            ->where('id_user', $iduser)
            ->get();

        return $propertiesQueue;
    }

    public function getpropertiesbymunicipio($id)
    {
        $properties = Properties::where('id_municipio', $id)->get();
        return response()->json($properties);
    }
}
