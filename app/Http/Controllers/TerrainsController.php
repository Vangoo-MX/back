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
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class TerrainsController extends Controller
{
    public function getAll()
    {
        return Terrains::all();
    }

    public function deleteTerrainHightlight($id)
    {
        if (TerrainsHighlights::destroy($id)) {
            return redirect()->route('admin.highlights.terrains');
        } else {
            return response()->json(['error' => 'Agenda entry not found'], 404);
        }
    }

    public function addTerrainHightlight(Request $request)
    {
        try {
            TerrainsHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_property' => $request->id_property,
            ]);

            return redirect()->route('admin.highlights.terrains');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderTerrainHightlight(Request $request)
    {
        $highlight = TerrainsHighlights::where('id_property', $request->id_property)->first();

        if ($highlight) {
            $highlight->update([
                'num_order' => $request->num_order,
            ]);

            return redirect()->route('admin.highlights.terrains');
        }

        return response()->json(['error' => 'Entry for property with id ' . $request->id . ' not found'], 404);
    }


    public function getTerrainCard($id)
    {
        return Terrains::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'parkings',
            'area_terrain',
            'description',
            'services',
            'views',
            'images',
        ])->find($id);
    }

    public function getMultiTerrainCard($array)
    {
        $ids = str_contains($array, ',') ? explode(',', $array) : [$array];

        return Terrains::select([
            'id',
            'title',
            'price',
            'location',
            'id_pais',
            'parkings',
            'area_terrain',
            'description',
            'services',
            'views',
            'images',
        ])->whereIn('id', $ids)->get();
    }

    public function getTerrainsImagesCards()
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card']
        ])->get();
    }

    public function getTerrainsImagesDetail($id)
    {
        return Images::where([
            ['type_property', '=', 'property'],
            ['category', '=', 'card'],
            ['id_property', '=', $id]
        ])->get();
    }

    public function rejectTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(['status_aproved' => 2]);
        return redirect()->route('admin.queueTerrains');
    }

    public function revisionTerrainQueue($id)
    {
        TerrainsQueue::where('id', $id)->update(['status_aproved' => 3]);
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

        foreach (File::allFiles($sourcePath) as $file) {
            File::move($file->getRealPath(), $destinationPath . $file->getFilename());
        }

        File::deleteDirectory($sourcePath, true);

        $terrainQueue->delete();

        return redirect()->route('admin.queueTerrains');
    }


    public function deleteTerrainQueue($id)
    {
        TerrainsQueue::findOrFail($id)->delete();
        return redirect()->route('admin.queue');
    }

    public function deleteTerrain($id)
    {
        if ($highlight = TerrainsHighlights::where('id_property', $id)->first()) {
            $highlight->delete();
        }

        $terrain = Terrains::findOrFail($id);
        $directoryPath = public_path("storage/img/posts/terrains/{$terrain->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
        }

        $terrain->delete();
        return redirect()->route('admin.terrains');
    }

    public function deleteImage(Request $request, $terrainId, $imageId)
    {
        $imagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $imageId . '.webp';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $terrain = Terrains::findOrFail($terrainId);
            $terrain->images -= 1;
            $terrain->save();

            for ($i = $imageId + 1; $i <= $terrain->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . $i . '.webp';
                $newImagePath = 'public/img/posts/terrains/' . $terrainId . '/' . ($i - 1) . '.webp';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return redirect()->back()->with('error', 'La imagen no fue encontrada.');
        }
    }

    public function deactiveTerrain($id)
    {
        Terrains::findOrFail($id)->update(['status' => 0]);
        return redirect()->route('admin.terrains');
    }

    public function activeTerrain($id)
    {
        Terrains::findOrFail($id)->update(['status' => 1]);
        return redirect()->route('admin.terrains');
    }

    public function getTerrainsByMunicipio($id)
    {
        return response()->json(Terrains::where('id_municipio', $id)->get());
    }

    public function updateTerrains(Request $request, Terrains $terrains)
    {
        $colonia = Colonias::find($request->id_colonia);
        $municipio = Municipios::find($request->id_municipio);
        $estado = Estados::find($terrains->id_estado);
        $location = $colonia->nombre . ', ' . $municipio->nombre . ', ' . $estado->nombre;

        $images = $terrains->images;

        if ($request->hasFile('images')) {
            $numImages = $images + sizeof($request->file('images'));
        } else {
            $numImages = $images;
        }

        $terrains->update([
            'title' => $request->title,
            'operation_type' => $request->operation_type,
            'price' => $request->price,
            'description' => $request->description,
            'parkings' => $request->parkings,
            'map' => $request->map,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'num_int' => $request->num_int,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
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
                $path = storage_path('app/public/img/posts/terrains/' . $request->id . '/');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);

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
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$order}.webp")) {
                    rename($path . "/{$order}.webp", $path . "/{$order}temp.webp");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/terrains/' . $terrains->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.webp")) {
                    rename($path . "/{$key}temp.webp", $path . "/{$order}.webp");
                }
            }
        }

        return redirect()->route('admin.detailsTerrains', $terrains)->with('success', 'Propiedad actualizada correctamente');
    }
}
