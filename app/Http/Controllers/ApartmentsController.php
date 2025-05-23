<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Apartments;
use App\Models\ApartmentsQueue;
use App\Models\ApartmentsHighlights;
use App\Models\Images;
use Illuminate\Support\Facades\File;


class ApartmentsController extends Controller
{
    public function getAll()
    {
        return Apartments::all();
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
