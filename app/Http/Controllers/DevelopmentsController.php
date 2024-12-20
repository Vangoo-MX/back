<?php

namespace App\Http\Controllers;

use App\Http\Requests\DevelopmentRequest;
use Exception;
use Illuminate\Http\Request;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsApartments;
use App\Models\Images;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Log;

class DevelopmentsController extends Controller
{
    public function getAll()
    {
        return Developments::all();
    }

    public function getDevelopmentsVerticalHightlights()
    {
        $highlightIds = DevelopmentsHighlights::orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? Developments::whereIn('id', $highlightIds)
            ->where('mode', 'vertical')
            ->get()
            : collect();
    }


    public function getDevelopmentsHorizontalHightlights()
    {
        $highlightIds = DevelopmentsHighlights::orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? Developments::whereIn('id', $highlightIds)
            ->where('mode', 'horizontal')
            ->get()
            : collect();
    }

    public function getDevelopmentsVerticalHightlightFromMunicipio($id)
    {
        $highlightIds = DevelopmentsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? Developments::whereIn('id', $highlightIds)
            ->where('id_municipio', $id)
            ->where('mode', 'vertical')
            ->get()
            : collect();
    }

    public function getDevelopmentsHorizontalHightlightFromMunicipio($id)
    {
        $highlightIds = DevelopmentsHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? Developments::whereIn('id', $highlightIds)
            ->where('id_municipio', $id)
            ->where('mode', 'horizontal')
            ->get()
            : collect();
    }

    public function deleteDevHightlight($id)
    {
        $highlight = DevelopmentsHighlights::find($id);

        if (!$highlight) {
            return response()->json(['error' => 'Agenda entry not found'], 404);
        }

        $highlight->delete();

        return redirect()->route('admin.highlights.developments');
    }

    public function addDevHightlight(Request $request)
    {
        try {
            DevelopmentsHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_development' => $request->id_development,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return redirect()->route('admin.highlights.developments');
    }

    public function orderDevHightlight(Request $request)
    {
        $highlight = DevelopmentsHighlights::where('id_development', $request->id)->first();

        if (!$highlight) {
            return response()->json([
                'error' => 'Entry for property with ID ' . $request->id . ' not found'
            ], 404);
        }

        $highlight->update([
            'num_order' => $request->num_order
        ]);

        return redirect()->route('admin.highlights.developments');
    }

    public function getDevsByMunicipio($id)
    {
        $developments = Developments::where('id_municipio', $id)->get();
        return response()->json($developments);
    }

    public function getDevelopmentsImagesCards()
    {
        return Images::where('type_property', 'development')
            ->where('category', 'card')
            ->get();
    }

    public function getDevelopmentsImagesDetail($id)
    {
        return Images::where('type_property', 'property')
            ->where('category', 'card')
            ->where('id_property', $id)
            ->get();
    }


    public function getDevelopment($id)
    {
        return Developments::where('id', $id)
            ->get();
    }

    public function getDevCard($id)
    {
        return Developments::select('id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'views', 'images')
            ->find($id);
    }

    public function getMultiDevCard($array)
    {
        $list = str_contains($array, '-') ? explode('-', $array) : [$array];

        return Developments::select('id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'views', 'images')
            ->whereIn('id', $list)
            ->get();
    }

    public function getDevelopmentsRelated($id)
    {

        $development = Developments::find($id);

        if (!$development) {
            return collect();
        }

        return Developments::where('status', $development->status)
            ->where('id_municipio', $development->id_municipio)
            ->take(10)
            ->get();
    }

    public function getDevSearch($estado = "0", $municipio = "0", $colonia = "0", $status = "0", $min = 0, $max = 0)
    {

        $search = Developments::query();

        if ($estado != "0") {
            $search = $search->where('id_estado', $estado);
        }
        if ($municipio != "0") {
            $search = $search->where('id_municipio', $municipio);
        }
        if ($colonia != "0") {
            $search = $search->where('id_colonia', $colonia);
        }

        if ($min != 0 || $max != 0) {
            if ($max == 0) {
                $search = $search->where('price_min', '<=', $min)->where('price_max', '>=', $min);
            } else {
                $search = $search->where(function ($query) use ($min, $max) {
                    $query->whereBetween('price_min', [$min, $max])
                        ->orWhereBetween('price_max', [$min, $max])
                        ->orWhere(function ($subQuery) use ($min, $max) {
                            $subQuery->where('price_min', '<=', $min)
                                ->where('price_max', '>=', $max);
                        });
                });
            }
        }

        if ($status == "presale") {
            $search = $search->where('status', 'presale');
        } elseif ($status == "sale") {
            $search = $search->where('status', 'sale');
        }

        $search = $search->paginate(50);

        return $search;
    }

    public function storeDev(DevelopmentRequest $request)
    {
        $development = new Developments;
        $development->title = $request->title;
        $development->status = $request->status;
        $development->price_min = $request->price_min;
        $development->price_max = $request->price_max;
        $development->description = $request->description;
        $development->availability = $request->availability;
        $development->financing = $request->financing;
        $development->mode = $request->mode;
        $development->id_estado = $request->id_estado;
        $development->id_municipio = $request->id_municipio;
        $development->id_colonia = $request->id_colonia;
        $development->street = $request->street;
        $development->num_ext = $request->num_ext;

        $estado = Estados::find($request->id_estado)->nombre;
        $municipio = Municipios::find($request->id_municipio)->nombre;
        $colonia = Colonias::find($request->id_colonia)->nombre;

        $development->location = $colonia . ', ' . $municipio . ', ' . $estado;

        $development->cp = $request->cp;
        $development->map = $request->map;
        $development->map_lat = $request->map_lat;
        $development->map_long = $request->map_long;
        $development->area = $request->area;
        $development->amenities = $request->amenities;
        $development->commission_percentage = $request->commission_percentage;
        $development->id_user = Auth::user()->id;

        if ($request->hasFile('images')) {
            $development->images = count($request->file('images'));
        }

        $development->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);

                $imageName = Str::slug($index + 1) . '.webp';
                if ($image->getClientOriginalExtension() === 'webp') {
                    $image->move($path, $imageName);
                } else {
                    $imageWebp = Image::make($image->getRealPath())
                        ->encode('webp', 90);

                    $imageWebp->save($path . $imageName);
                }
            }
        }

        $key = 1;
        foreach ($request->option as $option) {
            $apartment = new DevelopmentsApartments;
            $apartment->id_development = $development->id;
            $apartment->title = $option['title'];
            $apartment->price = $option['price'];
            $apartment->rooms = $option['rooms'];
            $apartment->bathrooms = $option['bathrooms'];
            $apartment->parkings = $option['parkings'];
            $apartment->area = $option['area'];
            if ($request->file('imageoption.' . $key) && is_array($request->file('imageoption.' . $key))) {
                $apartment->image_plans = sizeof($request->file('imageoption.' . $key));
            } elseif ($request->file('imageoption.' . $key) && !is_array($request->file('imageoption.' . $key))) {
                $apartment->image_plans = 1;
            } else {
                $apartment->image_plans = 0;
            }

            if ($option['num_available']) {
                $apartment->num_available = $option['num_available'];
            } else {
                $apartment->num_available = 0;
            }

            if (isset($request->imageoption[$key]) && $request->hasFile('imageoption.' . $key)) {
                $directory = 'public/img/posts/developments/' . $development->id . '/' . 'plans/';
                $nameimg = Str::slug($key) . ".webp";
                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/' . 'plans/');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);

                $extension = $request->file('imageoption.' . $key)->getClientOriginalExtension();

                if (strtolower($extension) !== 'webp') {
                    $imageWebp = Image::make($request->file('imageoption.' . $key)->getRealPath())
                        ->encode('webp', 90);
                    $imageWebp->save($path . $nameimg);
                } else {
                    $request->file('imageoption.' . $key)->storeAs($directory, $nameimg);
                }
            }

            $apartment->save();
            $key++;
        }

        return redirect()->route('admin.developments');
    }

    public function editdev(DevelopmentRequest $request)
    {
        $development = Developments::findOrFail($request->id);
        $development->title = $request->title;
        $development->status = $request->status;
        $development->price_min = $request->price_min;
        $development->price_max = $request->price_max;
        $development->description = $request->description;
        $development->availability = $request->availability;
        $development->financing = $request->financing;
        $development->mode = $request->mode;
        $development->id_estado = $request->id_estado;
        $development->id_municipio = $request->id_municipio;
        $development->id_colonia = $request->id_colonia;
        $development->street = $request->street;
        $development->num_ext = $request->num_ext;

        $estado = Estados::where('id', $request->id_estado)->get();
        $estado = $estado[0]['nombre'];

        $municipio = Municipios::where('id', $request->id_municipio)->get();
        $municipio = $municipio[0]['nombre'];

        $colonia = Colonias::where('id', $request->id_colonia)->get();
        $colonia = $colonia[0]['nombre'];

        $development->location = $colonia . ', ' . $municipio . ', ' . $estado;

        $development->cp = $request->cp;
        $development->map = $request->map;
        $development->map_lat = $request->map_lat;
        $development->map_long = $request->map_long;
        $development->area = $request->area;
        $development->amenities = $request->amenities;
        $development->commission_percentage = $request->commission_percentage;

        if ($request->hasFile('images')) {
            $development->images = $request->num_images + sizeof($request->file('images'));
        } else {
            $development->images = $request->num_images;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/');
                $nameimg = Str::slug($request->num_images + $index + 1) . ".webp";

                if ($image->getClientOriginalExtension() === 'webp') {
                    $image->move($path, $nameimg);
                } else {
                    $imageWebp = Image::make($image->getRealPath())
                        ->encode('webp', 90);

                    $imageWebp->save($path . $nameimg);
                }
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id);
                $key = $index;
                $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                foreach ($extensions as $ext) {
                    if (file_exists($path . "/{$order}.{$ext}")) {
                        rename($path . "/{$order}.{$ext}", $path . "/{$order}temp.{$ext}");
                    }
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id);
                $key = $index;
                $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                foreach ($extensions as $ext) {
                    if (file_exists($path . "/{$key}temp.{$ext}")) {
                        rename($path . "/{$key}temp.{$ext}", $path . "/{$order}.{$ext}");
                    }
                }
            }
        }

        $development->save();

        $key = 1;
        Log::info('Datos recibidos en optionapp:', $request->optionapp);
        if ($request->optionapp) {
            Log::info('Entrando al foreach de optionapp.');
            foreach ($request->optionapp as $option) {
                Log::info('Iteración en foreach:', ['option' => $option]);
                if (!isset($option['id'])) {
                    continue;
                }

                $appartment = DevelopmentsApartments::findOrFail($option['id']);
                $appartment->title = $option['title'] ?? $appartment->title;
                $appartment->price = $option['price'] ?? $appartment->price;
                $appartment->rooms = $option['rooms'] ?? $appartment->rooms;
                $appartment->bathrooms = $option['bathrooms'] ?? $appartment->bathrooms;
                $appartment->parkings = $option['parkings'] ?? $appartment->parkings;
                $appartment->area = $option['area'] ?? $appartment->area;

                if ($request->file('imageoption.' . $key) && is_array($request->file('imageoption.' . $key))) {
                    $appartment->image_plans = sizeof($request->file('imageoption.' . $key));
                } elseif ($request->file('imageoption.' . $key) && !is_array($request->file('imageoption.' . $key))) {
                    $appartment->image_plans = 1;
                } else {
                    $appartment->image_plans = 0;
                }

                $appartment->num_available = $option['num_available'] ?? 0;

                if (isset($request->imageoption[$key]) && $request->hasFile('imageoption.' . $key)) {
                    $directory = 'public/img/posts/developments/' . $development->id . '/' . 'plans/';
                    $nameimg = Str::slug($key) . ".webp";
                    $path = storage_path('app/public/img/posts/developments/' . $development->id . '/' . 'plans/');

                    $extension = $request->file('imageoption.' . $key)->getClientOriginalExtension();

                    if (strtolower($extension) !== 'webp') {
                        $imageWebp = Image::make($request->file('imageoption.' . $key)->getRealPath())
                            ->encode('webp', 90);
                        $imageWebp->save($path . $nameimg);
                    } else {
                        $request->file('imageoption.' . $key)->storeAs($directory, $nameimg);
                    }
                }

                $appartment->save();
                $key++;
            }
        }

        if ($request->optionapp) {
            $key = sizeof($request->optionapp) + 1;
        } else {
            $key = 0;
        }

        if ($request->option) {
            foreach ($request->option as $option) {
                $appartment = new DevelopmentsApartments;
                $appartment->id_development = $development->id;
                $appartment->title = $option['title'];
                $appartment->price = $option['price'];
                $appartment->rooms = $option['rooms'];
                $appartment->bathrooms = $option['bathrooms'] ?? $appartment->bathrooms;
                $appartment->parkings = $option['parkings'];
                $appartment->area = $option['area'];
                if ($request->file('imageoption.' . $key) && is_array($request->file('imageoption.' . $key))) {
                    $appartment->image_plans = sizeof($request->file('imageoption.' . $key));
                } elseif ($request->file('imageoption.' . $key) && !is_array($request->file('imageoption.' . $key))) {
                    $appartment->image_plans = 1;
                } else {
                    $appartment->image_plans = 0;
                }

                if ($option['num_available']) {
                    $appartment->num_available = $option['num_available'];
                } else {
                    $appartment->num_available = 0;
                }


                if (isset($request->imageoption[$key]) && $request->hasFile('imageoption.' . $key)) {
                    $directory = 'public/img/posts/developments/' . $development->id . '/' . 'plans/';
                    $nameimg = Str::slug($key) . ".webp";
                    $path = storage_path('app/public/img/posts/developments/' . $development->id . '/' . 'plans/');

                    $extension = $request->file('imageoption.' . $key)->getClientOriginalExtension();

                    if (strtolower($extension) !== 'webp') {
                        $imageWebp = Image::make($request->file('imageoption.' . $key)->getRealPath())
                            ->encode('webp', 90);
                        $imageWebp->save($path . $nameimg);
                    } else {
                        $request->file('imageoption.' . $key)->storeAs($directory, $nameimg);
                    }
                }

                $appartment->save();
                $key++;
            }
        }


        return redirect()->route('admin.developments');
    }

    public function deleteImage(Request $request, $developmentId, $imageId)
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $imageDeleted = false;

        $development = Developments::findOrFail($developmentId);

        foreach ($extensions as $extension) {
            $imagePath = 'public/img/posts/developments/' . $developmentId . '/' . $imageId . '.' . $extension;

            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
                $imageDeleted = true;
                $development->decrement('images');
                break;
            }
        }

        if (!$imageDeleted) {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }

        for ($i = $imageId + 1; $i <= $development->images + 1; $i++) {
            foreach ($extensions as $extension) {
                $oldImagePath = 'public/img/posts/developments/' . $developmentId . '/' . $i . '.' . $extension;
                $newImagePath = 'public/img/posts/developments/' . $developmentId . '/' . ($i - 1) . '.' . $extension;

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                    break;
                }
            }
        }
        return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
    }

    public function deleteDev($id)
    {
        DevelopmentsHighlights::where('id_development', $id)->delete();

        $dev = Developments::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/developments/{$dev->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            sleep(1);
            rmdir($directoryPath);
        }
        DevelopmentsApartments::where('id_development', $id)->delete();
        $dev->delete();

        return redirect()->back();
    }
}
