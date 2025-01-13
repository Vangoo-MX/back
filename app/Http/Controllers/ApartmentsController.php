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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ApartmentsController extends Controller
{
    public function getAll()
    {
        return Apartments::all();
    }

    public function getApartmentsHightlights()
    {
        $highlightIds = ApartmentsHighlights::orderBy('num_order', 'asc')
            ->pluck('id_property');

        return $highlightIds->isNotEmpty()
            ? Apartments::whereIn('id', $highlightIds)->get()
            : collect();
    }


    public function getApartmentsHightlightFromMunicipio($id)
    {
        $highlightIds = ApartmentsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_property');

        return $highlightIds->isNotEmpty()
            ? Apartments::whereIn('id', $highlightIds)->get()
            : collect();
    }

    public function deleteApartmentHightlight($id)
    {
        if (ApartmentsHighlights::destroy($id)) {
            return redirect('overview/apartments-highlights');
        } else {
            return response()->json(['error' => 'No se pudo eliminar el registro'], 404);
        }
    }

    public function addApartmentHightlight(Request $request)
    {
        try {
            ApartmentsHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_property' => $request->id_property,
            ]);

            return redirect('overview/apartments-highlights');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderApartmentHightlight(Request $request)
    {
        $highlight = ApartmentsHighlights::where('id_property', $request->id)
            ->first();

        if ($highlight) {
            $highlight->update([
                'num_order' => $request->num_order,
            ]);
            return redirect('overview/apartments-highlights');
        }

        return response()->json(['error' => 'No se encontró el registro'], 404);
    }


    public function getApartmentCard($id)
    {
        return Apartments::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'rooms',
            'parkings',
            'bathrooms',
            'area',
            'description',
            'views',
            'images'
        ])->find($id);
    }

    public function getMultiApartmentCard($array)
    {
        return Apartments::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'rooms',
            'parkings',
            'bathrooms',
            'area',
            'description',
            'views',
            'images'
        ])->whereIn('id', $array)->get();
    }

    public function getApartmentsImagesCards()
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card']
        ])->get();
    }

    public function getApartmentsImagesDetail($id)
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card'],
            ['id_property', '=', $id]
        ])->get();
    }

    public function getApartment($id)
    {
        return Apartments::where('id', $id)->get();
    }

    public function getApartmentQueueEP($id)
    {
        return ApartmentsQueue::where('id', $id)->get();
    }

    public function getApartmentsRelated($id)
    {
        $apartments = Apartments::where('id', $id)
            ->get();

        if (!$apartments) {
            return collect();
        }

        return Apartments::where('id', '<>', $id)
            ->where('bathrooms', $apartments->bathrooms)
            ->where('rooms', $apartments->rooms)
            ->where('id_municipio', $apartments->id_municipio)
            ->take(10)
            ->get();
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
        ApartmentsQueue::where('id', $id)->update(['status_aproved' => 2]);
        return redirect()->route('admin.queueApartments');
    }

    public function revisionApartmentQueue($id)
    {
        ApartmentsQueue::where('id', $id)->update(['status_aproved' => 3]);
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

        foreach (File::allFiles($sourcePath) as $file) {
            File::move($file->getRealPath(), $destinationPath . $file->getFilename());
        }

        File::deleteDirectory($sourcePath, true);

        $apartmentQueue->delete();

        return redirect()->route('admin.queueApartments');
    }


    public function deleteApartmentQueue($id)
    {

        ApartmentsQueue::findOrFail($id)->delete();
        return redirect()->route('admin.queue');
    }

    public function deleteApartmentQueueEP($id)
    {
        $apartmentQueue = ApartmentsQueue::findOrFail($id);
        $directoryPath = public_path("storage/img/postsqueue/apartments/{$apartmentQueue->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $apartmentQueue->delete();
        return json_encode("success");
    }

    public function deleteApartment($id)
    {
        if ($highlight = ApartmentsHighlights::where('id_property', $id)->first()) {
            $highlight->delete();
        }

        $apartment = Apartments::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/apartments/{$apartment->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $apartment->delete();
        return redirect()->route('admin.apartments');
    }

    public function deleteImage(Request $request, $apartmentId, $imageId)
    {
        $imagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . $imageId . '.webp';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $apartment = Apartments::findOrFail($apartmentId);
            $apartment->images -= 1;
            $apartment->save();

            for ($i = $imageId + 1; $i <= $apartment->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . $i . '.webp';
                $newImagePath = 'public/img/posts/apartments/' . $apartmentId . '/' . ($i - 1) . '.webp';

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
        Apartments::findOrFail($id)->update(['status' => 0]);
        return redirect()->route('admin.apartments');
    }

    public function activeApartment($id)
    {
        Apartments::findOrFail($id)->update(['status' => 1]);
        return redirect()->route('admin.apartments');
    }

    public function deleteApartmentEP($id)
    {

        $apartment = Apartments::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/apartments/{$apartment->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
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

        if ($request->has('orderArray') && is_array($request->orderArray)) {
            $orderArray = $request->orderArray;

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/apartments/' . $request->id);
                $tempFile = $path . "/{$order}.webp";

                if (file_exists($tempFile)) {
                    rename($tempFile, $path . "/{$order}temp.webp");
                } else {
                    Log::warning("File not found during temp rename: {$tempFile}");
                }
            }

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/apartments/' . $request->id);
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

        $apartment->save();

        return json_encode($apartment->id);
    }

    public function getUserApartments($iduser)
    {
        return Apartments::select('id', 'title', 'price', 'location', 'views', 'images', 'status')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getUserApartmentsQueue($iduser)
    {
        return ApartmentsQueue::select('id', 'title', 'price', 'location', 'images', 'status_aproved')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getApartmentsByMunicipio($id)
    {
        return response()->json(Apartments::where('id_municipio', $id)->get());
    }

    public function updateApartments(Request $request, Apartments $apartments)
    {
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($apartments->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;

        $images = $apartments->images;

        if ($request->hasFile('images')) {
            $numImages = $images + sizeof($request->file('images'));
        } else {
            $numImages = $images;
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
                $path = storage_path('app/public/img/posts/apartments/' . $request->id . '/');
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
                $path = storage_path('app/public/img/posts/apartments/' . $apartments->id);
                $key = $index;
                if (file_exists($path . "/{$order}.webp")) {
                    rename($path . "/{$order}.webp", $path . "/{$order}temp.webp");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/apartments/' . $apartments->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.webp")) {
                    rename($path . "/{$key}temp.webp", $path . "/{$order}.webp");
                }
            }
        }

        return redirect()->route('admin.detailsApartments', $apartments)->with('success', 'Propiedad actualizada correctamente');
    }
}
