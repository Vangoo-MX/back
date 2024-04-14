<?php

use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use App\Models\Properties;
use App\Models\Developments;
use App\Models\User;

function estado($id){
    $return = Estados::where('id', $id)->get();
    return $return[0]['nombre'];
}

function municipio($id){
    $return = Municipios::where('id', $id)->get();
    return $return[0]['nombre'];
}


function colonia($id){
    $return = Colonias::where('id', $id)->get();
    return $return[0]['nombre'];
}

function property($id){
    $return = Properties::where('id', $id)->get();
    return $return;
}

function propertiesByMunicipio($id){
    $return = Properties::where('id_municipio', $id)->get();
    return $return;
}

function development($id){
    $return = Developments::where('id', $id)->get();
    return $return;
}

function user($id){
    $return = User::where('id', $id)->get();
    return $return;
}

function username($id){
    $return = User::where('id', $id)->get();
    if($return && isset($return[0])){
        $text = $return[0]['name'];
        $return = substr($return[0]['name'],0,10);
        if (strlen($text) > 10) {
            $return .= "..";
        }
        return $return;
    }else{
        return "-";
    }
}

function moneyFormat($numero) {
  $fmt = numfmt_create('es_MX', NumberFormatter::CURRENCY);
  return numfmt_format_currency($fmt, $numero, 'MXN');
}

function convertDate($date) {
    if($date){
        $date = strtotime($date);
        return date("d-m-Y", $date);
    }else{
        return "-";
    }
}

function limitString($string, $limit) {
    if (strlen($string) > $limit) {
        $string = substr($string, 0, $limit) . "..";
    }
    return $string;
}

