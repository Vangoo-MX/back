<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;

class InfoController extends Controller
{
    public function getEstados(){
       $estados = Estados::where('active', 1)
        ->get();
       
        return $estados;
    }

    public function getEstado($id){
       $estados = Estados::where('id', $id)
        ->get();
       
        return $estados;
    }

    public function getEstadoFromPais($id){
       $estados = Estados::where('id_pais', $id)
        ->where('active', 1)
        ->get();
       
        return $estados;
    }

    public function getMunicipios(){
       $municipios = Municipios::where('active', 1)
        ->get();
       
        return $municipios;
    }

    public function getMunicipio($id){
       $municipios = Municipios::where('id', $id)
        ->get();
       
        return $municipios;
    }

    public function getMunicipiosFromEstado($id){
       $municipios = Municipios::where('id_estado', $id)
        ->where('active', 1)
        ->get();
       
        return $municipios;
    }

    public function getColonia($id){
       $colonia = Colonias::where('id', $id)
        ->get();
       
        return $colonia;
    }

    public function getColoniasFromMunicipio($id){
        $colonias = Colonias::where('id_municipio', $id)
            ->orderBy('nombre', 'asc')
            ->get();
        
        return $colonias;
    }


    public function getEstadoMunicipioColonia($estado,$municipio,$colonia){

        $return = [];
        
        $estados = Estados::selectRaw('nombre')->where('id', $estado)->get();
        $municipios = Municipios::selectRaw('nombre')->where('id', $municipio)->get();
        $colonias = Colonias::selectRaw('nombre')->where('id', $colonia)->get();

        $return[0]['estado'] = $estados[0];
        $return[0]['municipio'] = $municipios[0];
        $return[0]['colonia'] = $colonias[0];


        return $return;
    }
}