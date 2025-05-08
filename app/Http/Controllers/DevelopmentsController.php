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

class DevelopmentsController extends Controller
{
    public function getAll()
    {
        return Developments::all();
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

        $images = $development->images;
        if ($request->hasFile('images')) {
            $development->images = $images + sizeof($request->file('images'));
        } else {
            $development->images = $images;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = storage_path('app/public/img/posts/developments/' . $development->id . '/');
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
            return redirect()->back()->with('error', 'La imagen no fue encontrada.');
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
        if (DevelopmentsHighlights::where('id_development', $id)->exists()) {
            DevelopmentsHighlights::where('id_development', $id)->delete();
        }

        $dev = Developments::findOrFail($id);
        if (!$dev) {
            return redirect()->back()->with('error', 'El desarrollo no existe.');
        }

        $directoryPath = public_path("storage/img/posts/developments/{$dev->id}");
        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath);
        }

        if (DevelopmentsApartments::where('id_development', $id)->exists()) {
            DevelopmentsApartments::where('id_development', $id)->delete();
        }

        $dev->delete();

        return redirect()->back();
    }
}
