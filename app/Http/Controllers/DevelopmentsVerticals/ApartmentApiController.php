<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentsApartments;
use Illuminate\Http\Request;

class ApartmentApiController extends Controller
{
    public function getApartments($id)
    {
        return DevelopmentsApartments::where('id_development', $id)
            ->get();
    }
}
