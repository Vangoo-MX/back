<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DevelopmentsApartments;
use App\Models\Images;

class DevelopmentsApartmentsController extends Controller
{
    public function getApartmentsFromDev($id)
    {
        return DevelopmentsApartments::where('id_development', $id)
            ->get();
    }

    public function getApartmentsImages($id)
    {
        $images = Images::where('type_property', 'apartment')
            ->where('category', 'plans')
            ->where('id_property', $id)
            ->get();

        return $images;
    }
}
