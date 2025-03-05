<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use App\Models\Images;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PropertiesController extends Controller
{
    public function getAll()
    {
        return Properties::all();
    }

    public function getPropertiesHightlights()
    {
        $highlightIds = PropertiesHighlights::orderBy('num_order', 'asc')
            ->pluck('id_property');

        return $highlightIds->isNotEmpty()
            ? Properties::whereIn('id', $highlightIds)->get()
            : collect();
    }


    public function getPropertiesHightlightFromMunicipio($id)
    {
        $highlightIds = PropertiesHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_property');

        return $highlightIds->isNotEmpty()
            ? Properties::whereIn('id', $highlightIds)->get()
            : collect();
    }

    public function deletePropertyHightlight($id)
    {
        if (PropertiesHighlights::destroy($id)) {
            return redirect('overview/properties-highlights');
        }

        return response()->json(['error' => 'Agenda entry not found'], 404);
    }

    public function addPropertyHightlight(Request $request)
    {
        try {
            PropertiesHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_property' => $request->id_property,
            ]);

            return redirect('overview/properties-highlights');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderPropertyHightlight(Request $request)
    {
        $highlight = PropertiesHighlights::where('id_property', $request->id)->first();

        if ($highlight) {
            $highlight->update(['num_order' => $request->num_order]);
            return redirect('overview/properties-highlights');
        }

        return response()->json(['error' => 'Entry for property with id ' . $request->id . ' not found'], 404);
    }


    public function getPropertyCard($id)
    {
        return Properties::select([
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

    public function getMultiPropertyCard($array)
    {

        $ids = str_contains($array, '-') ? explode('-', $array) : [$array];

        return Properties::select([
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
        ])->whereIn('id', $ids)->get();
    }

    public function getPropertiesImagesCards()
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card']
        ])->get();
    }

    public function getPropertiesImagesDetail($id)
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card'],
            ['id_property', '=', $id]
        ])->get();
    }

    public function getProperty($id)
    {
        return Properties::where('id', $id)
            ->get();
    }

    public function getPropertyQueueEP($id)
    {
        return PropertiesQueue::where('id', $id)
            ->get();
    }

    public function getPropertyRelated($id)
    {
        $property = Properties::find($id);

        if (!$property) {
            return collect();
        }

        $priceRange = [
            $property->price * 0.8,
            $property->price * 1.2
        ];

        return Properties::where('id', '<>', $id)
            ->whereBetween('price', $priceRange)
            ->where('id_municipio', $property->id_municipio)
            ->take(10)
            ->get();
    }

    public function getPropertySearch($estado = "0", $municipio = "0", $colonia = "0", $type = "alltypes", $min = 0, $max = 0)
    {

        $search = Properties::query();

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

    public function rejectPropertyQueue($id)
    {
        PropertiesQueue::where('id', $id)->update(['status_aproved' => 2]);
        return redirect()->route('admin.queue');
    }

    public function revisionPropertyQueue($id)
    {
        PropertiesQueue::where('id', $id)->update(['status_aproved' => 3]);
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

        foreach (File::allFiles($sourcePath) as $file) {
            File::move($file->getRealPath(), $destinationPath . $file->getFilename());
        }

        File::deleteDirectory($sourcePath, true);

        $propertyQueue->delete();

        return redirect()->route('admin.queue');
    }

    public function deletePropertyQueue($id)
    {
        PropertiesQueue::findOrFail($id)->delete();
        return redirect()->route('admin.queue');
    }

    public function deletePropertyQueueEP($id)
    {
        $propertyQueue = PropertiesQueue::findOrFail($id);
        $directoryPath = public_path("storage/img/postsqueue/properties/{$propertyQueue->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $propertyQueue->delete();
        return response()->json("success");
    }

    public function deleteProperty($id)
    {
        if ($highlight = PropertiesHighlights::where('id_property', $id)->first()) {
            $highlight->delete();
        }

        $property = Properties::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/properties/{$property->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $property->delete();
        return redirect()->route('admin.properties');
    }

    public function deleteImage(Request $request, $propertieId, $imageId)
    {
        $imagePath = 'public/img/posts/properties/' . $propertieId . '/' . $imageId . '.webp';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $propertie = Properties::findOrFail($propertieId);
            $propertie->images -= 1;
            $propertie->save();

            for ($i = $imageId + 1; $i <= $propertie->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/properties/' . $propertieId . '/' . $i . '.webp';
                $newImagePath = 'public/img/posts/properties/' . $propertieId . '/' . ($i - 1) . '.webp';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }
    }

    public function deactiveProperty($id)
    {
        Properties::findOrFail($id)->update(['status' => 0]);
        return redirect()->route('admin.properties');
    }

    public function activeProperty($id)
    {
        Properties::findOrFail($id)->update(['status' => 1]);
        return redirect()->route('admin.properties');
    }

    public function deletePropertyEP($id)
    {
        $property = Properties::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/properties/{$property->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $property->delete();
        return response()->json("success");
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
        if (isset($request->propertyBathrooms)) {
            $property->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $property->rooms = $request->propertyRooms;
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

    public function imagesPropertyQueue(Request $request)
    {

        if ($request->hasFile('image')) {
            $imagen = $request->file('image');
            $directory = 'public/img/postsqueue/properties/' . $request->id . '/';
            $nameimg = Str::slug($request->index) . "." . $imagen->getClientOriginalExtension();
            $path = storage_path('app/public/img/postsqueue/properties/' . $request->id . '/');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            chmod($path, 0755);
            $imagen->storeAs($directory, $nameimg);
        }

        return json_encode('success');
    }

    public function deleteImagesPropertyQueue(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json(['error' => 'ID inválido'], 400);
        }
        $imageNames = $request->imageNames;
        $route = public_path("storage/img/postsqueue/properties/{$id}/");
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
            $property = PropertiesQueue::find($id);
            if ($property) {
                $property->images = max(0, $property->images - $deletedCount);
                $property->save();
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
        if (isset($request->propertyBathrooms)) {
            $property->bathrooms = $request->propertyBathrooms;
        }
        if (isset($request->propertyRooms)) {
            $property->rooms = $request->propertyRooms;
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
        if (isset($request->propertyExactLocation)) {
            $property->no_exact_location = $request->propertyExactLocation == true ? 0 : 1;
        }
        if (isset($request->number_images)) {
            $property->images += $request->number_images;
        }
        if (isset($request->status_aproved)) {
            $property->status_aproved = $request->status_aproved;
        }

        if ($request->has('orderArray') && is_array($request->orderArray)) {
            $orderArray = $request->orderArray;

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/properties/' . $request->id);
                $tempFile = $path . "/{$order}.webp";

                if (file_exists($tempFile)) {
                    rename($tempFile, $path . "/{$order}temp.webp");
                } else {
                    Log::warning("File not found during temp rename: {$tempFile}");
                }
            }

            foreach ($orderArray as $index => $order) {
                $path = storage_path('app/public/img/postsqueue/properties/' . $request->id);
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

        $property->save();

        return json_encode($property->id);
    }

    public function getUserProperties($iduser)
    {
        return Properties::select('id', 'title', 'price', 'location', 'views', 'images')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getUserPropertiesQueue($iduser)
    {
        return PropertiesQueue::select('id', 'title', 'price', 'location', 'views', 'images', 'status_aproved')
            ->where('id_user', $iduser)
            ->get();
    }

    public function getpropertiesbymunicipio($id)
    {
        return response()->json(Properties::where('id_municipio', $id)->get());
    }

    public function updateProperties(Request $request, Properties $propiedad)
    {
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($propiedad->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;

        $images = $propiedad->images;

        if ($request->hasFile('images')) {
            $numImages = $images + sizeof($request->file('images'));
        } else {
            $numImages = $images;
        }

        $propiedad->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'price' => $request->price,
            'description' => $request->description,
            'rooms' => $request->rooms,
            'bathrooms' => $request->bathrooms,
            'parkings' => $request->parkings,
            'map' => $request->map,
            'area' => $request->area,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'num_int' => $request->num_int,
            'cp' => $request->cp,
            'amenities' => $request->amenities,
            'sell_type' => $request->sell_type,
            'share_conditions' => $request->share_conditions,
            'antiquity' => $request->antiquity,
            'location' => $location,
            'images' => $numImages
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/properties/' . $request->id . '/');
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
                $path = storage_path('app/public/img/posts/properties/' . $propiedad->id);
                $key = $index;
                if (file_exists($path . "/{$order}.webp")) {
                    rename($path . "/{$order}.webp", $path . "/{$order}temp.webp");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/properties/' . $propiedad->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.webp")) {
                    rename($path . "/{$key}temp.webp", $path . "/{$order}.webp");
                }
            }
        }

        return redirect()->route('admin.details', $propiedad)->with('success', 'Propiedad actualizada correctamente');
    }
}
