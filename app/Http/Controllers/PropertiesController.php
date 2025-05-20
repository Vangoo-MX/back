<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use App\Models\PropertiesQueue;

class PropertiesController extends Controller
{
    public function getPropertyQueueEP($id)
    {
        return PropertiesQueue::where('id', $id)
            ->get();
    }

    public function deletePropertyQueue($id)
    {
        PropertiesQueue::findOrFail($id)->delete();
        return redirect()->route('admin.queue');
    }

    public function getPropertyQueue($id)
    {
        $propertyQueue = PropertiesQueue::where('id', $id)->get();
        return view('admin.propertyqueue', compact('propertyQueue'));
    }

    public function getpropertiesbymunicipio($id)
    {
        return response()->json(Properties::where('id_municipio', $id)->get());
    }
}
