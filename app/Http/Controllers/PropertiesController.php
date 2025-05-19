<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\PropertiesHighlights;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\Colonias;
use Illuminate\Support\Facades\Storage;

class PropertiesController extends Controller
{
    public function deletePropertyHightlight($id)
    {
        if (PropertiesHighlights::destroy($id)) {
            return redirect()->route('admin.highlights.properties');
        }

        return response()->json(['error' => 'Agenda entry not found'], 404);
    }

    public function addPropertyHightlight(Request $request)
    {
        try {
            PropertiesHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                'id_property' => $request->id_property,
            ]);

            return redirect()->route('admin.highlights.properties');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderPropertyHightlight(Request $request)
    {
        $highlight = PropertiesHighlights::where('id_property', $request->id)->first();

        if ($highlight) {
            $highlight->update(['num_order' => $request->num_order]);
            return redirect()->route('admin.highlights.properties');
        }

        return response()->json(['error' => 'Entry for property with id ' . $request->id . ' not found'], 404);
    }

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
