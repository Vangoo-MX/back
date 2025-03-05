<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Http\Requests\DevelopmentRequest;
use App\Models\Colonias;
use App\Models\DevelopmentsHorizontalApartments;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Models\Estados;
use App\Models\Images;
use App\Models\Municipios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DevelopmentsHorizontalController extends Controller
{
    public function getAll()
    {
        return DevelopmentsHorizontals::all();
    }

    public function getDevelopmentsHorizontalHightlights()
    {
        $highlightIds = DevelopmentsHorizontalHighlights::orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? DevelopmentsHorizontals::whereIn('id', $highlightIds)
            ->where('mode', 'horizontal')
            ->get()
            : collect();
    }

    public function getDevelopmentsHorizontalHightlightFromMunicipio($id)
    {
        $highlightIds = DevelopmentsHorizontalHighlights::where('id_municipio', $id)
            ->orderBy('num_order', 'asc')
            ->pluck('id_development')
            ->toArray();

        return !empty($highlightIds)
            ? DevelopmentsHorizontals::whereIn('id', $highlightIds)
            ->where('id_municipio', $id)
            ->where('mode', 'horizontal')
            ->get()
            : collect();
    }

    public function deleteDevHorizontalHightlight($id)
    {
        $highlight = DevelopmentsHorizontalHighlights::find($id);

        if (!$highlight) {
            return response()->json(['error' => 'No se encontró el desarrollo destacado.'], 404);
        }

        $highlight->delete();

        return redirect()->route('admin.highlights.developmentsHorizontal');
    }

    public function addDevHorizontalHightlight(Request $request)
    {
        try {
            DevelopmentsHorizontalHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_development' => $request->id_development,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al agregar el desarrollo destacado.'], 500);
        }

        return redirect()->route('admin.highlights.developmentsHorizontal');
    }

    public function orderDevHorizontalHightlights(Request $request)
    {
        $highlight = DevelopmentsHorizontalHighlights::where('id_development', $request->id)->first();

        if (!$highlight) {
            return response()->json([
                'error' => 'No se encontró el desarrollo destacado.'
            ], 404);
        }

        $highlight->update([
            'num_order' => $request->num_order
        ]);

        return redirect()->route('admin.highlights.developmentsHorizontal');
    }

    public function getDevsHorizontalByMunicipio($id)
    {
        $developments = DevelopmentsHorizontals::where('id_municipio', $id)->get();
        return response()->json($developments);
    }

    public function getDevelopmentsHorizontalImagesCards()
    {
        return Images::where('type_property', 'development')
            ->where('category', 'card')
            ->get();
    }

    public function getDevelopmentsHorizontalImagesDetail($id)
    {
        return Images::where('type_property', 'development')
            ->where('category', 'detail')
            ->where('id_property', $id)
            ->get();
    }

    public function getDevelopmentHorizontal($id)
    {
        return DevelopmentsHorizontals::where('id', $id)->get();
    }

    public function getDevHorizontalCard($id)
    {
        return DevelopmentsHorizontals::select('id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'views', 'images')
            ->find($id);
    }

    public function getMultiDevHorizontalCard($array)
    {
        $list = str_contains($array, '-') ? explode('-', $array) : [$array];

        return DevelopmentsHorizontals::select('id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'views', 'images')
            ->whereIn('id', $list)
            ->get();
    }

    public function getDevelopmentsHorizontalRelated($id)
    {
        $development = DevelopmentsHorizontals::where('id', $id)->first();

        if (!$development) {
            return collect();
        }

        $minPrice = floor($development->price_min * 0.8);
        $maxPrice = ceil($development->price_max * 1.2);

        return DevelopmentsHorizontals::where('status', $development->status)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('price_min', [$minPrice, $maxPrice])
                    ->orWhereBetween('price_max', [$minPrice, $maxPrice])
                    ->orWhere(function ($q) use ($minPrice, $maxPrice) {
                        $q->where('price_min', '<=', $minPrice)
                            ->where('price_max', '>=', $maxPrice);
                    });
            })
            ->where('id_municipio', $development->id_municipio)
            ->take(10)
            ->get();
    }

    public function getDevHorizontalSearch($estado = "0", $municipio = "0", $colonia = "0", $status = "0", $min = 0, $max = 0)
    {
        $search = DevelopmentsHorizontals::query();

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
            if ($max == 0) {
                $search->where('price_min', '<=', $min)->where('price_max', '>=', $min);
            } else {
                $search->where(function ($query) use ($min, $max) {
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
            $search->where('status', 'presale');
        } elseif ($status == "sale") {
            $search = $search->where('status', 'sale');
        }

        return $search->paginate(50);
    }

    public function storeDevHorizontal(DevelopmentRequest $request)
    {
        $development = new DevelopmentsHorizontals();
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
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/');

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
            $apartment = new DevelopmentsHorizontalApartments;
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
                $directory = 'public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/';
                $nameimg = Str::slug($key) . ".webp";
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/');
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
        return redirect()->route('admin.developmentsHorizontal');
    }

    public function editDevHorizontal(DevelopmentRequest $request)
    {
        $development = DevelopmentsHorizontals::findOrFail($request->id);
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

        $images = $development->images;
        if ($request->hasFile('images')) {
            $development->images = $images + sizeof($request->file('images'));
        } else {
            $development->images = $images;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/');
                $nameimg = Str::slug($images + $index + 1) . ".webp";

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
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id);
                $key = $index;
                $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                foreach ($extensions as $ext) {
                    if (file_exists($path . "/{$order}.{$ext}")) {
                        rename($path . "/{$order}.{$ext}", $path . "/{$order}temp.{$ext}");
                    }
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id);
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
        if ($request->optionapp) {
            foreach ($request->optionapp as $option) {
                if (!isset($option['id'])) {
                    continue;
                }

                $apartment = DevelopmentsHorizontalApartments::findOrFail($option['id']);
                $apartment->title = $option['title'] ?? $apartment->title;
                $apartment->price = $option['price'] ?? $apartment->price;
                $apartment->rooms = $option['rooms'] ?? $apartment->rooms;
                $apartment->bathrooms = $option['bathrooms'] ?? $apartment->bathrooms;
                $apartment->parkings = $option['parkings'] ?? $apartment->parkings;
                $apartment->area = $option['area'] ?? $apartment->area;

                if ($request->file('imageoption.' . $key) && is_array($request->file('imageoption.' . $key))) {
                    $apartment->image_plans = sizeof($request->file('imageoption.' . $key));
                } elseif ($request->file('imageoption.' . $key) && !is_array($request->file('imageoption.' . $key))) {
                    $apartment->image_plans = 1;
                } else {
                    $apartment->image_plans = 0;
                }

                $apartment->num_available = $option['num_available'] ?? 0;

                if (isset($request->imageoption[$key]) && $request->hasFile('imageoption.' . $key)) {
                    $directory = 'public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/';
                    $nameimg = Str::slug($key) . ".webp";
                    $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/');

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
        }

        if ($request->optionapp) {
            $key = sizeof($request->optionapp) + 1;
        } else {
            $key = 0;
        }

        if ($request->option) {
            foreach ($request->option as $option) {
                $apartment = new DevelopmentsHorizontalApartments;
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
                    $directory = 'public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/';
                    $nameimg = Str::slug($key) . ".webp";
                    $path = storage_path('app/public/img/posts/developmentsHorizontal/' . $development->id . '/' . 'plans/');
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
        }

        return redirect()->route('admin.developmentsHorizontal');
    }

    public function deleteImage(Request $request, $developmentId, $imageId)
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $imageDeleted = false;

        $development = DevelopmentsHorizontals::findOrFail($developmentId);

        foreach ($extensions as $extension) {
            $imagePath = 'public/img/posts/developmentsHorizontal/' . $developmentId . '/' . $imageId . '.' . $extension;

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
                $oldImagePath = 'public/img/posts/developmentsHorizontal/' . $developmentId . '/' . $i . '.' . $extension;
                $newImagePath = 'public/img/posts/developmentsHorizontal/' . $developmentId . '/' . ($i - 1) . '.' . $extension;

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                    break;
                }
            }
        }
        return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
    }

    public function deleteDevHorizontal($id)
    {
        DevelopmentsHorizontalHighlights::where('id_development', $id)->delete();

        $development = DevelopmentsHorizontals::findOrFail($id);

        $path = public_path("storage/img/posts/developmentsHorizontal/ {$development->id}");

        if (is_dir($path)) {
            File::deleteDirectory($path, true);
            sleep(1);
            rmdir($path);
        }

        DevelopmentsHorizontalApartments::where('id_development', $id)->delete();
        $development->delete();

        return redirect()->back();
    }
}
