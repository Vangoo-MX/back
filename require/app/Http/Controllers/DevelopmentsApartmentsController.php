<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DevelopmentsApartments;
use App\Models\Images;

class DevelopmentsApartmentsController extends Controller
{
    public function getApartmentsFromDev($id){
        $apartments = DevelopmentsApartments::where('id_development',$id)
        ->get();
        return $apartments;
    }

    public function getApartmentsImages($id){
        $images = Images::where('type_property', 'apartment')
            ->where('category', 'plans')
            ->where('id_property', $id)
            ->get();
        
        return $images;
    }

}
