<?php

namespace App\Http\Controllers;

use App\Http\Requests\LotsRequest;
use Exception;
use Illuminate\Http\Request;
use App\Models\Lots;
use App\Models\Images;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use App\Models\LotsHighlights;
use PHPUnit\TextUI\XmlConfiguration\Loader;

class LotsController
{
    public function getAll()
    {
        $lots = Lots::all();
        return $lots;
    }

    public function getLotsByMunicipality($id)
    {
        $lot = Lots::where('id_municipio', $id)->get();
        return response()->json($lot);
    }

    public function getLot($id)
    {
        $lots = Lots::where('id', $id)
            ->get();

        return $lots;
    }

    public function storeLot(Request $request)
    {
        $estado = Estados::find($request->id_estado)->nombre;
        $municipio = Municipios::find($request->id_municipio)->nombre;
        $colonia = Colonias::find($request->id_colonia)->nombre;
        $location = $colonia . ', ' . $municipio . ', ' . $estado;
        $lot = Lots::create([
            'title' => $request->title,
            'type_lots' => $request->type_lots,
            'developers' => $request->developers,
            'status' => $request->status,
            'number_lots' => $request->number_lots,
            'lots_min' => $request->lots_min,
            'lots_max' => $request->lots_max,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'availability' => $request->availability,
            'financing' => $request->financing,
            'type_terrain' => $request->type_terrain,
            'slope' => $request->slope,
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'num_ext' => $request->num_ext,
            'location' => $location,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'broad' => $request->broad,
            'largue' => $request->largue,
            'price_mt2' => $request->price_mt2,
            'amenities' => $request->amenities,
            'initial_fee' => $request->initial_fee,
            'commission_percentage' => $request->commission_percentage,
            'id_user' => Auth::user()->id,
            'images' => $request->hasFile('images') ? sizeof($request->file('images')) : 0
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $directory = 'public/img/posts/lots/' . $lot->id . '/';
                $nameimg = Str::slug($index + 1) . "." . $image->getClientOriginalExtension();

                $path = storage_path('app/public/img/posts/lots/' . $lot->id . '/');

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                chmod($path, 0755);
                $image->storeAs($directory, $nameimg);
            }
        }
        return redirect()->route('admin.lots');
    }

    public function editLot(Request $request)
    {
        $lot = Lots::find($request->id);

        if (!$lot) {
            return redirect()->back()->with('error', 'Lote no encontrado.');
        }

        $estado = Estados::find($request->id_estado)->nombre;
        $municipio = Municipios::find($request->id_municipio)->nombre;
        $colonia = Colonias::find($request->id_colonia)->nombre;
        $location = $colonia . ', ' . $municipio . ', ' . $estado;

        if ($request->hasFile('images')) {
            $numImages = $lot->images + sizeof($request->file('images'));
        } else {
            $numImages = $lot->images;
        }

        $lot->update([
            'title' => $request->title,
            'type_lots' => $request->type_lots,
            'developers' => $request->developers,
            'status' => $request->status,
            'number_lots' => $request->number_lots,
            'lots_min' => $request->lots_min,
            'lots_max' => $request->lots_max,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'availability' => $request->availability,
            'financing' => $request->financing,
            'type_terrain' => $request->type_terrain,
            'slope' => $request->slope,
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'num_ext' => $request->num_ext,
            'location' => $location,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'broad' => $request->broad,
            'largue' => $request->largue,
            'price_mt2' => $request->price_mt2,
            'amenities' => $request->amenities,
            'initial_fee' => $request->initial_fee,
            'commission_percentage' => $request->commission_percentage,
            'images' => $numImages,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                $nameimg = Str::slug($lot->images + $index + 1) . "." . $image->getClientOriginalExtension();
                $image->storeAs('public/img/posts/lots/' . $lot->id . '/', $nameimg);
            }
        }

        if ($request->orderimg) {
            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/lots/' . $lot->id);
                $key = $index;
                if (file_exists($path . "/{$order}.jpg")) {
                    rename($path . "/{$order}.jpg", $path . "/{$order}temp.jpg");
                }
            }

            foreach ($request->orderimg as $index => $order) {
                $path = storage_path('app/public/img/posts/lots/' . $lot->id);
                $key = $index;
                if (file_exists($path . "/{$key}temp.jpg")) {
                    rename($path . "/{$key}temp.jpg", $path . "/{$order}.jpg");
                }
            }
        }
        return redirect()->route('admin.lots');
    }

    public function deleteImage(Request $request, $lotId, $imageId)
    {
        $imagePath = 'public/img/posts/lots/' . $lotId . '/' . $imageId . '.jpg';

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);

            $lot = Lots::findOrFail($lotId);
            $lot->images -= 1;
            $lot->save();

            for ($i = $imageId + 1; $i <= $lot->images + 1; $i++) {
                $oldImagePath = 'public/img/posts/lots/' . $lotId . '/' . $i . '.jpg';
                $newImagePath = 'public/img/posts/lots/' . $lotId . '/' . ($i - 1) . '.jpg';

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                }
            }

            return redirect()->back()->with('success', 'La imagen se eliminó correctamente.');
        } else {
            return response()->json(['error' => 'Imagen no encontrada.'], 404);
        }
    }

    public function deleteLot($id)
    {

        $lot = Lots::findOrFail($id);

        $directoryPath = public_path("storage/img/posts/lots/{$lot->id}");

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
            // Esperar 1 segundo antes de intentar eliminar la carpeta
            sleep(1);
            rmdir($directoryPath);
        }

        $lot->delete();

        return redirect()->back();
    }

    public function getLotsByMunicipio($id)
    {
        $lot = Lots::where('id_municipio', $id)->get();
        return response()->json($lot);
    }

    public function addLotHightlight(Request $request)
    {
        try {
            $h = new LotsHighlights();
            $h->id_estado = 19;
            $h->id_municipio = $request->id_municipio;
            $h->id_lot = $request->id_lot;
            $h->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return redirect('overview/lots-highlights');
    }

    public function deleteLotHightlight($id)
    {
        $h = LotsHighlights::find($id);

        if ($h) {
            $h->delete();
            return redirect('overview/lots-highlights');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function orderLotHightlight(Request $request)
    {
        $idProperty = $request->id;

        $h = LotsHighlights::where('id_lot', $idProperty)->first();

        if ($h) {
            $h->num_order = $request->num_order;
            $h->save();

            return redirect('overview/lots-highlights');
        } else {
            return json_encode('error: entry for property with id ' . $idProperty . ' not found');
        }
    }
}
