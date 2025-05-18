<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Storage;

class PropertiesController extends Controller
{
    public function deletePropertyHightlight($id)
    {
        if (PropertiesHighlights::destroy($id)) {
            return redirect()->route('admin.highlights.properties');
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

            return redirect()->route('admin.highlights.properties');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderPropertyHightlight(Request $request)
    {
        $highlight = PropertiesHighlights::where('id_property', $request->id)->first();

        if ($highlight) {
            $highlight->update(['num_order' => $request->num_order]);
            return redirect()->route('admin.highlights.properties');
        }

        return response()->json(['error' => 'Entry for property with id ' . $request->id . ' not found'], 404);
    }

    public function getPropertyQueueEP($id)
    {
        return PropertiesQueue::where('id', $id)
            ->get();
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
            return redirect()->back()->with('error', 'La imagen no fue encontrada.');
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

    public function getPropertyQueue($id)
    {
        $propertyQueue = PropertiesQueue::where('id', $id)->get();
        return view('admin.propertyqueue', compact('propertyQueue'));
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
