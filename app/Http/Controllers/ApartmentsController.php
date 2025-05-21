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

    public function deleteApartmentHightlight($id)
    {
        if (ApartmentsHighlights::destroy($id)) {
            return redirect()->route('admin.highlights.apartments');
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

            return redirect()->route('admin.highlights.apartments');
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
            return redirect()->route('admin.highlights.apartments');
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
        $ids = str_contains($array, ',') ? explode(',', $array) : [$array];
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
        ])->whereIn('id', $ids)->get();
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
}
