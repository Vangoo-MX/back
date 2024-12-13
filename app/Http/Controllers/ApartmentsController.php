<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Apartments;
use App\Models\ApartmentsQueue;
use App\Models\ApartmentsHighlights;
use App\Models\Images;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Storage;

class ApartmentsController extends Controller
{
    public function getAll()
    {
        $Apartments = Apartments::all();
        return $Apartments;
    }

    public function getApartmentsHightlights()
    {
        $highlights = ApartmentsHighlights::orderBy('num_order', 'asc')->get();

        if (sizeof($highlights) > 0) {
            $highlightIds = $highlights->pluck('id_property')->toArray();
            $highlights = Apartments::whereIn('id', $highlightIds)->get();
        } else {
            $highlights = [];
        }

        return $highlights;
    }


    public function getApartmentsHightlightFromMunicipio($id)
    {
        $highlight = ApartmentsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->get();

        if (sizeof($highlight) > 0) {
            $Apartments = Apartments::select();
            foreach ($highlight as $value) {
                $Apartments = $Apartments->orwhere('id', $value->id_property);
            }
            $Apartments = $Apartments->get();
        } else {
            $Apartments = [];
        }

        return $Apartments;
    }

    public function deleteApartmentHightlight($id)
    {
        $h = ApartmentsHighlights::find($id);

        if ($h) {
            $h->delete();
            return redirect('overview/apartments-highlights');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function addApartmentHightlight(Request $request)
    {
        try {
            $h = new ApartmentsHighlights();
            $h->id_estado = 19;
            $h->id_municipio = $request->id_municipio;
            $h->id_property = $request->id_property;
            $h->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return redirect('overview/apartments-highlights');
    }

    public function orderApartmentHightlight(Request $request)
    {
        $apartmentID = $request->id;

        $h = ApartmentsHighlights::where('id_property', $apartmentID)->first();

        if ($h) {
            $h->num_order = $request->num_order;
            $h->save();

            return redirect('overview/apartments-highlights');
        } else {
            return json_encode('error: entry for property with id ' . $apartmentID . ' not found');
        }
    }


    public function getApartmentCard($id)
    {
        $Apartments = Apartments::selectRaw('id,title,price,location,id_pais,rooms,dev_type,parkings,bathrooms,area,description,views,images')
            ->where('id', $id)
            ->get();

        return $Apartments;
    }

    public function getMultiApartmentCard($array)
    {

        if (str_contains($array, '-')) {
            $list = explode('-', $array);
        } else {
            $list[] = $array;
        }

        $Apartments = Apartments::selectRaw('id,title,price,location,id_pais,rooms,dev_type,parkings,bathrooms,area,description,views,images');

        foreach ($list as $value) {
            $Apartments = $Apartments->orWhere('id', $value);
        }

        $Apartments = $Apartments->get();

        return $Apartments;
    }

    public function getApartmentsImagesCards()
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->get();

        return $images;
    }

    public function getApartmentsImagesDetail($id)
    {
        $images = Images::where('type_property', 'property')
            ->where('category', 'card')
            ->where('id_property', $id)
            ->get();

        return $images;
    }

    public function getApartment($id)
    {
        $Apartments = Apartments::where('id', $id)
            ->get();

        return $Apartments;
    }

    public function getApartmentQueueEP($id)
    {
        $apartmentQueue = ApartmentsQueue::where('id', $id)->get();
        return $apartmentQueue;
    }

    public function getApartmentsRelated($id)
    {
        $apartments = Apartments::where('id', $id)
            ->get();
        $apartmentsRelated = Apartments::where('id', '<>', $id)
            ->where('bathrooms', $apartments[0]->bathrooms)
            ->where('rooms', $apartments[0]->rooms)
            ->where('id_municipio', $apartments[0]->id_municipio)
            ->take(10)
            ->get();

        return $apartmentsRelated;
    }

    public function getApartmentSearch($estado = "0", $municipio = "0", $colonia = "0", $type = "alltypes", $min = 0, $max = 0)
    {

        $search = Apartments::select();

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

    public function rejectApartmentQueue($id)
    {
        ApartmentsQueue::where('id', $id)->update(array('status_aproved' => 2));
        return redirect()->route('admin.queueApartments');
    }

    public function revisionApartmentQueue($id)
    {
        ApartmentsQueue::where('id', $id)->update(array('status_aproved' => 3));
        return redirect()->route('admin.queueApartments');
    }

    public function aprovedApartmentsQueue(Request $request)
    {
        $apartmentQueue = ApartmentsQueue::findOrFail($request->id);
        $apartmentData = Arr::except($apartmentQueue->toArray(), ['id']);
        $newApartment = Apartments::create($apartmentData);
        $sourcePath = public_path("storage/img/postsqueue/apartments/" . $request->id . "/");
        $destinationPath = public_path("storage/img/posts/apartments/" . $newApartment->id . "/");

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        $files = File::allFiles($sourcePath);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            File::move($sourcePath . $filename, $destinationPath . $filename);
        }

        if (is_dir($sourcePath)) {
            File::deleteDirectory($sourcePath, true);
            sleep(1);
            rmdir($sourcePath);
        }

        $apartmentQueue->delete();

        return redirect()->route('admin.queueApartments');
    }


    public function deleteApartmentQueue($id)
    {

        $propertyQueue = ApartmentsQueue::findOrFail($id);
        $propertyQueue->delete();
        return redirect()->route('admin.queue');
    }

    public function deleteApartmentQueueEP($id)
    {
        $apartmentQueue = ApartmentsQueue::findOrFail($id);
        $directoryPath = public_path("storage/img/postsqueue/apartments/{$apartmentQueue->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            sleep(1);
            rmdir($directoryPath);
        }

        $apartmentQueue->delete();
        return json_encode("success");
    }

    public function deleteApartment($id)
    {
        $highlight = ApartmentsHighlights::where('id_property', $id)->first();

        if ($highlight) {
            $highlight->delete();
        }
        $apartment = Apartments::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/apartments/{$apartment->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $apartment->delete();
        return redirect()->route('admin.apartments');
    }

    public function deleteImage(Request $request, $apartmentId, $imageId)
    {
        $imagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . $imageId . '.jpg';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $apartment = Apartments::findOrFail($apartmentId);
            $apartment->images -= 1;
            $apartment->save();

            for ($i = $imageId + 1; $i <= $apartment->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . $i . '.jpg';
                $newImagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . ($i - 1) . '.jpg';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }
    }

    public function deactiveApartment($id)
    {

        $apartment = Apartments::findOrFail($id);

        $apartment->status = 0;

        $apartment->save();
        return redirect()->route('admin.apartments');
    }

    public function activeApartment($id)
    {

        $aparment = Apartments::findOrFail($id);

        $aparment->status = 1;

        $aparment->save();
        return redirect()->route('admin.apartments');
    }

    public function deleteApartmentEP($id)
    {

        $apartment = Apartments::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/apartments/{$apartment->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $apartment->delete();
        return json_encode("success");
    }

    public function postApartmentsQueue(Request $request)
    {

        $apartment = new ApartmentsQueue();
        $apartment->title = $request->propertyTitle;
        $apartment->price = $request->propertySellPrice;
        if (isset($request->propertyIntNumber)) {
            $apartment->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $apartment->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $apartment->street = $request->propertyStreet;
        }

        $apartment->id_colonia = $request->propertyColonia;

        $apartment->id_municipio = $request->propertyMunicipio;

        $apartment->id_estado = $request->propertyEstado;

        $apartment->id_pais = 1;
        if (isset($request->propertyCP)) {
            $apartment->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $apartment->location = $colonia . ', ' . $municipio . ', ' . $estado;


        if (isset($request->propertyAreaConstruction)) {
            $apartment->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyBathrooms)) {
            $apartment->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $apartment->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyDevType)) {
            $apartment->dev_type = $request->propertyDevType;
        }
        if (isset($request->propertyParkings)) {
            $apartment->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $apartment->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $apartment->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $apartment->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $apartment->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $apartment->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyAmenities)) {
            $apartment->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $apartment->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $apartment->price_maintenance = $request->propertyPriceMaintenance;
        }
        if (isset($request->propertyOperationType)) {
            $apartment->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $apartment->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $apartment->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $apartment->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyExactLocation)) {
            $apartment->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }

        if (isset($request->number_images)) {
            $apartment->images = $request->number_images;
        }
        if (isset($request->id_user)) {
            $apartment->id_user = $request->id_user;
        }
        $apartment->views = 0;

        $apartment->save();

        return json_encode($apartment->id);
    }

    public function imagesApartmentsQueue(Request $request)
    {

        if ($request->hasFile('image')) {
            $imagen = $request->file('image');
            $directory = 'public/img/postsqueue/apartments/' . $request->id . '/';
            $nameimg = Str::slug($request->index) . "." . $imagen->getClientOriginalExtension();
            $path = storage_path('app/public/img/postsqueue/apartments/' . $request->id . '/');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            chmod($path, 0755);
            $imagen->storeAs($directory, $nameimg);
        }

        return json_encode('success');
    }

    public function deleteImagesApartmentsQueue(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json(['error' => 'ID inválido'], 400);
        }
        $imageNames = $request->imageNames;
        $route = public_path("storage/img/postsqueue/apartments/{$id}/");
        $extensions = ['jpg', 'jpeg', 'png'];
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
            $apartment = ApartmentsQueue::find($id);
            if ($apartment) {
                $apartment->images = max(0, $apartment->images - $deletedCount);
                $apartment->save();
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

    public function updateApartmentsQueue(Request $request)
    {

        $apartment = ApartmentsQueue::findOrFail($request->id);

        $apartment->title = $request->propertyTitle;
        $apartment->price = $request->propertySellPrice;

        if (isset($request->propertyIntNumber)) {
            $apartment->num_int = $request->propertyIntNumber;
        }
        if (isset($request->propertyExtNumber)) {
            $apartment->num_ext = $request->propertyExtNumber;
        }
        if (isset($request->propertyStreet)) {
            $apartment->street = $request->propertyStreet;
        }

        $apartment->id_colonia = $request->propertyColonia;

        $apartment->id_municipio = $request->propertyMunicipio;

        $apartment->id_estado = $request->propertyEstado;

        $apartment->id_pais = 1;
        if (isset($request->propertyCP)) {
            $apartment->cp = $request->propertyCP;
        }


        $estado = Estados::where('id', $request->propertyEstado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->propertyMunicipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->propertyColonia)->get();
        $colonia = $colonia[0]['nombre'];

        $apartment->location = $colonia . ', ' . $municipio . ', ' . $estado;

        if (isset($request->propertyAreaConstruction)) {
            $apartment->area = $request->propertyAreaConstruction;
        }
        if (isset($request->propertyBathrooms)) {
            $apartment->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $apartment->rooms = $request->propertyRooms;
        }
        if (isset($request->propertyDevType)) {
            $apartment->dev_type = $request->propertyDevType;
        }
        if (isset($request->propertyParkings)) {
            $apartment->parkings = $request->propertyParkings;
        }
        if (isset($request->propertyDescription)) {
            $apartment->description = $request->propertyDescription;
        }
        if (isset($request->propertyMap)) {
            $apartment->map = $request->propertyMap;
        }
        if (isset($request->propertyMapLat)) {
            $apartment->map_lat = $request->propertyMapLat;
        }
        if (isset($request->propertyMapLong)) {
            $apartment->map_long = $request->propertyMapLong;
        }
        if (isset($request->propertyAgeConstruction)) {
            $apartment->antiquity = $request->propertyAgeConstruction;
        }
        if (isset($request->propertyAmenities)) {
            $apartment->amenities = $request->propertyAmenities;
        }
        if (isset($request->propertyFloor)) {
            $apartment->floor = $request->propertyFloor;
        }
        if (isset($request->propertyPriceMaintenance)) {
            $apartment->price_maintenance = $request->propertyPriceMaintenance;
        }
        if (isset($request->propertyOperationType)) {
            $apartment->operation_type = $request->propertyOperationType;
        }
        if (isset($request->propertyAmountPriceBasedM2)) {
            $apartment->price_m2 = $request->propertyAmountPriceBasedM2;
        }
        if (isset($request->propertySellType)) {
            $apartment->sell_type = $request->propertySellType;
        }
        if (isset($request->propertyShareConditions)) {
            $apartment->share_conditions = $request->propertySharedConditions;
        }
        if (isset($request->propertyExactLocation)) {
            $apartment->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }
        if (isset($request->number_images)) {
            $apartment->images += $request->number_images;
        }
        if (isset($request->status_aproved)) {
            $apartment->status_aproved = $request->status_aproved;
        }

        $apartment->save();

        return json_encode($apartment->id);
    }

    public function getUserApartments($iduser)
    {
        $Apartments = Apartments::selectRaw('id,title,price,location,views,images')
            ->where('id_user', $iduser)
            ->get();

        return $Apartments;
    }

    public function getUserApartmentsQueue($iduser)
    {
        $ApartmentsQueue = ApartmentsQueue::selectRaw('id,title,price,location,views,images,status_aproved')
            ->where('id_user', $iduser)
            ->get();

        return $ApartmentsQueue;
    }

    public function getApartmentsByMunicipio($id)
    {
        $Apartments = Apartments::where('id_municipio', $id)->get();
        return response()->json($Apartments);
    }

    public function updateApartments(Request $request, Apartments $apartments)
    {
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($apartments->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;
        if ($request->hasFile('images')) {
            $numImages = $apartments->images + sizeof($request->file('images'));
        } else {
            $numImages = $apartments->images;
        }
        $apartments->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'price' => $request->price,
            'price_maintenance' => $request->price_maintenance,
            'description' => $request->description,
            'rooms' => $request->rooms,
            'bathrooms' => $request->bathrooms,
            'parkings' => $request->parkings,
            'floor' => $request->floor,
            'dev_type' => $request->dev_type,
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
            'sell_type' => $request->sell_type,
            'share_conditions' => $request->share_conditions,
            'antiquity' => $request->antiquity,
            'location' => $location,
            'images' => $numImages
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                $nameimg = Str::slug($apartments->images + $index) . "." . $image->getClientOriginalExtension();
                $image->storeAs('public/img/posts/apartments/' . $apartments->id . '/', $nameimg);
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/apartments/' . $apartments->id);
                $key = $index;
                if (file_exists($path . "/{$order}.jpg")) {
                    rename($path . "/{$order}.jpg", $path . "/{$order}temp.jpg");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/apartments/' . $apartments->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.jpg")) {
                    rename($path . "/{$key}temp.jpg", $path . "/{$order}.jpg");
                }
            }
        }

        return redirect()->route('admin.detailsApartments', $apartments)->with('success', 'Propiedad actualizada correctamente');
    }
}
