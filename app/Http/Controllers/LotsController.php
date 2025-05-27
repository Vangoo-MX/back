<?php

namespace App\Http\Controllers;

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
use Intervention\Image\Facades\Image;

class LotsController
{
    public function getAll()
    {
        return Lots::all();
    }

    public function getLotsByMunicipality($id)
    {
        $lot = Lots::where('id_municipio', $id)->get();
        return response()->json($lot);
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
                $path = storage_path('app/public/img/posts/lots/' . $lot->id . '/');

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
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            $uploadPath = 'public/img/posts/lots/' . $lot->id . '/';

            foreach ($request->file('images') as $index => $image) {
                $filename = Str::uuid() . '.webp';
                $fullPath = $uploadPath . $filename;

                if ($image->getClientOriginalExtension() !== 'webp') {
                    Image::make($image)->encode('webp', 90)->save(storage_path('app/' . $fullPath));
                } else {
                    $image->storeAs($uploadPath, $filename);
                }

                $imagePaths[] = $filename;
            }

            $lot->images = $imagePaths;
            $lot->save();
        }
        return redirect()->route('admin.lots');
    }

    public function reorderImages(Request $request, Lots $lot)
    {
        $request->validate([
            'new_order' => 'required|array',
            'new_order.*' => 'string'
        ]);

        $newOrder = is_string($request->new_order)
            ? json_decode($request->new_order, true)
            : $request->new_order;

        foreach ($newOrder as $filename) {
            if (!in_array($filename, $lot->images)) {
                return response()->json([
                    'error' => 'El archivo ' . $filename . ' no pertenece a este lote'
                ], 422);
            }
        }

        try {
            $lot->update(['images' => $newOrder]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage(Request $request, $lotId, $imageId)
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $imageDeleted = false;

        $lot = Lots::findOrFail($lotId);

        foreach ($extensions as $extension) {
            $imagePath = 'public/img/posts/lots/' . $lotId . '/' . $imageId . '.' . $extension;

            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
                $imageDeleted = true;
                $lot->decrement('images');
                break;
            }
        }

        if (!$imageDeleted) {
            return redirect()->back()->with('error', 'La imagen no fue encontrada.');
        }

        for ($i = $imageId + 1; $i <= $lot->images + 1; $i++) {
            foreach ($extensions as $extension) {
                $oldImagePath = 'public/img/posts/lots/' . $lotId . '/' . $i . '.' . $extension;
                $newImagePath = 'public/img/posts/lots/' . $lotId . '/' . ($i - 1) . '.' . $extension;

                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, $newImagePath);
                    break;
                }
            }
        }

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function deleteLot($id)
    {
        LotsHighlights::where('id_lot', $id)->delete();

        $lot = Lots::findOrFail($id);

        $directoryPath = public_path('storage/img/posts/lots/' . $lot->id);

        if (is_dir($directoryPath)) {
            File::deleteDirectory($directoryPath, true);
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
            LotsHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_lot' => $request->id_lot,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return redirect()->route('admin.highlights.lots');
    }

    public function deleteLotHightlight($id)
    {
        $highlight = LotsHighlights::find($id);

        if (!$highlight) {
            return response()->json(['error' => 'No se encontró el destacado a eliminar.'], 404);
        }

        $highlight->delete();

        return redirect()->route('admin.highlights.lots');
    }

    public function orderLotHightlight(Request $request)
    {
        $highlight = LotsHighlights::where('id_lot', $request->id)->first();

        if (!$highlight) {
            return response()->json(['error' => 'No se encontró el destacado a ordenar.'], 404);
        }

        $highlight->update([
            'num_order' => $request->order,
        ]);

        return redirect()->route('admin.highlights.lots');
    }
}
