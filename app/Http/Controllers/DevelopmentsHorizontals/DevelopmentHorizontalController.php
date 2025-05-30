<?php

namespace App\Http\Controllers\DevelopmentsHorizontals;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentsHorizontalApartments;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevelopmentHorizontalController extends Controller
{
    use HandlesEstate;

    protected $model = DevelopmentsHorizontals::class;
    protected $highlightModel = DevelopmentsHorizontalHighlights::class;
    protected $apartmentModel = DevelopmentsHorizontalApartments::class;
    protected $directory = 'developmentsHorizontal';

    public function index()
    {
        $viewEstate = 'admin.horizontals.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.horizontals.create';
        return $this->createEstate($viewEstate);
    }

    public function store(Request $request)
    {
        $development = DevelopmentsHorizontals::create([
            'title' => $request->title,
            'status' => $request->status,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'availability' => $request->availability,
            'financing' => $request->financing,
            'mode' => $request->mode,
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'street' => $request->street,
            'num_ext' => $request->num_ext,
            'location' => $this->getLocation(
                $request->id_colonia,
                $request->id_municipio,
                $request->id_estado
            ),
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'area' => $request->area,
            'amenities' => $request->amenities,
            'commission_percentage' => $request->commission_percentage,
            'id_user' => Auth::user()->id,
            'images' => $request->hasFile('images') ? count($request->file('images')) : 0,
        ]);

        $this->handleImageProcessing($request, $development, false);
        $this->processApartments($request, $development);

        return redirect()->route('horizontals.index')->with('success', __('messages.development_created'));
    }

    public function edit($id)
    {
        $viewEstate = 'admin.horizontals.edit';
        return $this->editDevelopment($viewEstate, $id);
    }
}
