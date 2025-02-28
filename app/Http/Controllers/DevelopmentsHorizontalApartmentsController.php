<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentsHorizontalApartments;
use App\Models\Images;

class DevelopmentsHorizontalApartmentsController extends Controller
{
    public function getApartmentsFromDevHorizontal($id)
    {
        return DevelopmentsHorizontalApartments::where('id_development', $id)
            ->get();
    }

    public function getApartmentsImagesHorizontal($id)
    {
        $images = Images::where('type_property', 'apartment')
            ->where('category', 'plans')
            ->where('id_property', $id)
            ->get();

        return $images;
    }
}
