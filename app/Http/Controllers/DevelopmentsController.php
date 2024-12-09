<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsApartments;
use App\Models\Properties;
use App\Models\Images;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;

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

        $highlight->update(['num_order' => $request->num_order]);

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
        return Developments::find($id);
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

    public function storeDev(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5|max:100',
            'price_min' => 'required|numeric|lte:price_max',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|min:10|max:500',
            'availability' => 'required|date',
            'street' => 'required|min:3|max:20',
            'num_ext' => 'required|numeric',
            'cp' => 'required|numeric',
            'amenities' => 'min:3',
            'area' => 'required|numeric',
            'commission_percentage' => 'required|numeric',
            'id_municipio' => 'required',
            // 'images' => 'required|array',
            // 'images.*' => 'image|mimes:jpeg,jpg|max:2048',
        ], [
            'title.required' => 'El título es obligatorio',
            'title.min' => 'El título debe tener mas de 10 caracteres',
            'title.max' => 'El título debe tener menos de 100 caracteres',
            'price_min.required' => 'El precio mínimo es obligatorio',
            'price_min.numeric' => 'El precio mínimo debe ser un numero',
            'price_min.lte' => 'El precio mínimo debe ser menor o igual al precio máximo',
            'price_max.required' => 'El precio máximo es obligatorio',
            'price_max.numeric' => 'El precio máximo debe ser un numero',
            'price_max.gte' => 'El precio máximo debe ser mayor o igual al precio mínimo',
            'description.required' => 'La descripción es obligatoria',
            'description.min' => 'La descripción debe tener mas de 10 caracteres',
            'description.max' => 'La descripción debe tener menos de 500 caracteres',
            'availability.required' => 'La fecha de disponibilidad es obligatoria',
            'availability.date' => 'La fecha de disponibilidad debe ser una fecha valida',
            'street.required' => 'La calle es requerida',
            'street.min' => 'La calle debe tener mas de 3 caracteres',
            'street.max' => 'La calle debe tener menos de 100 caracteres',
            'num_ext.required' => 'El número exterior es requerido',
            'num_ext.numeric' => 'El numero exterior solo puede ser un numero',
            'cp.required' => 'El codigo postal es requerido',
            'cp.numeric' => 'El codigo solo puede ser un numero',
            'amenities.min' => 'Ingrese minimo una amenidad',
            'area.required' => 'La medida del area es requerida',
            'area.numeric' => 'La medida del area debe ser un número',
            'commission_percentage.required' => 'El porcentaje de comisión es requerido',
            'commission_percentage.numeric' => 'El porcentaje de comisión debe ser un número',
            'id_municipio.required' => 'El municipio es requerido',
            // 'imagen.required' => 'Sube al menos una imagen',
            // 'images.*.image' => 'Cada archivo debe ser una imagen.',
            // 'images.*.mimes' => 'Cada imagen debe ser de tipo jpeg o jpg.',
            // 'images.*.max' => 'Cada imagen no puede ser mayor de 2MB.',
        ]);
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
        $development->id_user = Auth::user()->id;
        if ($request->hasFile('images')) {
            $development->images = sizeof($request->file('images'));
        }
        $development->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $directory = 'public/img/posts/developments/' . $development->id . '/';
                $nameimg = Str::slug($index + 1) . "." . $image->getClientOriginalExtension();

                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);
                $image->storeAs($directory, $nameimg);
            }
        }

        $key = 1;

        foreach ($request->option as $option) {
            $appartment = new DevelopmentsApartments;
            $appartment->id_development = $development->id;
            $appartment->title = $option['title'];
            $appartment->price = $option['price'];
            $appartment->rooms = $option['rooms'];
            $appartment->bathrooms = $option['bathrooms'];
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
                $nameimg = Str::slug($key) . "." . $request->file('imageoption.' . $key)->getClientOriginalExtension();
                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/' . 'plans/');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);
                $request->file('imageoption.' . $key)->storeAs($directory, $nameimg);
            }

            $appartment->save();
            $key++;
        }

        return redirect()->route('admin.developments');
    }

    public function editdev(Request $request)
    {
        $request->validate([
            'title' => 'required|min:10|max:100',
            'price_min' => 'required|numeric|lte:price_max',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|min:10|max:500',
            'availability' => 'required|date',
            'street' => 'required|min:3|max:20',
            'num_ext' => 'required|numeric',
            'cp' => 'required|numeric',
            'amenities' => 'min:3',
            'area' => 'required|numeric',
            'commission_percentage' => 'required|numeric',
            'id_municipio' => 'required',
            // 'images' => 'array',
            // 'images.*' => 'image|mimes:jpeg,jpg|max:2048'
        ], [
            'title.required' => 'El título es obligatorio',
            'title.min' => 'El título debe tener mas de 10 caracteres',
            'title.max' => 'El título debe tener menos de 100 caracteres',
            'price_min.required' => 'El precio mínimo es obligatorio',
            'price_min.numeric' => 'El precio mínimo debe ser un numero',
            'price_min.lte' => 'El precio mínimo debe ser menor o igual al precio máximo',
            'price_max.required' => 'El precio máximo es obligatorio',
            'price_max.numeric' => 'El precio máximo debe ser un numero',
            'price_max.gte' => 'El precio máximo debe ser mayor o igual al precio mínimo',
            'description.required' => 'La descripción es obligatoria',
            'description.min' => 'La descripción debe tener mas de 10 caracteres',
            'description.max' => 'La descripción debe tener menos de 500 caracteres',
            'availability.required' => 'La fecha de disponibilidad es obligatoria',
            'availability.date' => 'La fecha de disponibilidad debe ser una fecha valida',
            'street.required' => 'La calle es requerida',
            'street.min' => 'La calle debe tener mas de 3 caracteres',
            'street.max' => 'La calle debe tener menos de 100 caracteres',
            'num_ext.required' => 'El número exterior es requerido',
            'num_ext.numeric' => 'El numero exterior solo puede ser un numero',
            'cp.required' => 'El codigo postal es requerido',
            'cp.numeric' => 'El codigo solo puede ser un numero',
            'amenities.min' => 'Ingrese minimo una amenidad',
            'area.required' => 'La medida del area es requerida',
            'area.numeric' => 'La medida del area debe ser un número',
            'commission_percentage.required' => 'El porcentaje de comisión es requerido',
            'commission_percentage.numeric' => 'El porcentaje de comisión debe ser un número',
            'id_municipio.required' => 'El municipio es requerido',
            // 'images.*.image' => 'Cada archivo debe ser una imagen.',
            // 'images.*.mimes' => 'Cada imagen debe ser de tipo jpeg o jpg.',
            // 'images.*.max' => 'Cada imagen no puede ser mayor de 2MB.',
        ]);
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

                $nameimg = Str::slug($request->num_images + $index + 1) . "." . $image->getClientOriginalExtension();
                $image->storeAs('public/img/posts/developments/' . $request->id . '/', $nameimg);
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id);
                $key = $index;
                if (file_exists($path . "/{$order}.jpg")) {
                    rename($path . "/{$order}.jpg", $path . "/{$order}temp.jpg");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.jpg")) {
                    rename($path . "/{$key}temp.jpg", $path . "/{$order}.jpg");
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
                    $nameimg = Str::slug($key) . "." . $request->file('imageoption.' . $key)->getClientOriginalExtension();
                    $request->file('imageoption.' . $key)->storeAs('public/img/posts/developments/' . $development->id . '/' . 'plans/', $nameimg);
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
                $appartment->bathrooms = $option['bathrooms'];
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
                    //foreach ($request->file('imageoption.' . $key) as $i => $image) {
                    $nameimg = Str::slug($key) . "." . $request->file('imageoption.' . $key)->getClientOriginalExtension();
                    $request->file('imageoption.' . $key)->storeAs('public/img/posts/developments/' . $development->id . '/' . 'plans/', $nameimg);
                    //}
                }

                $appartment->save();
                $key++;
            }
        }


        return redirect()->route('admin.developments');
    }

    public function deleteImage(Request $request, $developmentId, $imageId)
    {
        $imagePath = 'public/img/posts/developments/' . $developmentId . '/' . $imageId . '.jpg';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $development = Developments::findOrFail($developmentId);
            $development->images -= 1;
            $development->save();

            for ($i = $imageId + 1; $i <= $development->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/developments/' . $developmentId . '/' . $i . '.jpg';
                $newImagePath = 'public/img/posts/developments/' . $developmentId . '/' . ($i - 1) . '.jpg';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }
    }

    public function deleteDev($id)
    {
        $highlight = DevelopmentsHighlights::where('id_development', $id)->first();

        if ($highlight) {
            $highlight->delete();
        }

        $dev = Developments::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/developments/{$dev->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $dev->delete();

        $apartments = DevelopmentsApartments::where('id_development', $id);
        $apartments->delete();

        return redirect()->back();
    }
}
