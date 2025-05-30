<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\Developments;
use App\Models\DevelopmentsApartments;
use App\Models\DevelopmentsHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevelopmentVerticalController extends Controller
{
    use HandlesEstate;
    protected $model = Developments::class;
    protected $highlightModel = DevelopmentsHighlights::class;
    protected $apartmentModel = DevelopmentsApartments::class;
    protected $directory = 'developments';

    public function index()
    {
        $viewEstate = 'admin.verticals.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.verticals.create';
        return $this->createEstate($viewEstate);
    }

    public function store(Request $request)
    {
        $development = Developments::create([
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
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'area' => $request->area,
            'amenities' => $request->amenities,
            'commission_percentage' => $request->commission_percentage,
            'id_user' => Auth::user()->id,
            'images' => $request->hasFile('images') ? count($request->file('images')) : 0,
        ]);

        if ($request->hasFile('images')) {
            $this->handleImageProcessing($request, $development, false);
        }

        $this->processApartments($request, $development);

        return redirect()->route('verticals.index')->with('success', 'Desarrollo vertical creado exitosamente.');
    }

    public function edit($id)
    {
        $viewEstate = 'admin.verticals.edit';
        return $this->editDevelopment($id, $viewEstate);
    }

    public function update(Request $request, $id)
    {
        $development = Developments::findOrFail($id);

        $updateData = $request->only([
            'title',
            'status',
            'price_min',
            'price_max',
            'description',
            'availability',
            'financing',
            'mode',
            'id_estado',
            'id_municipio',
            'id_colonia',
            'street',
            'num_ext',
            'map_lat',
            'map_long',
            'area',
            'amenities',
            'commission_percentage'
        ]);

        $this->handleImageProcessing($request, $development, true);

        $updateData['images'] = $development->images + ($request->hasFile('images') ? count($request->file('images')) : 0);

        $updateData['location'] = $this->getLocation(
            $request->id_colonia,
            $request->id_municipio,
            $request->id_estado
        );

        $development->update($updateData);

        $submittedIds = [];
        if ($request->optionapp) {
            foreach ($request->optionapp as $option) {
                if (!empty($option['id'])) {
                    $submittedIds[] = $option['id'];
                }
            }
        }

        $this->updateApartments(
            $request,
            $development,
            'optionapp',
            'option',
            'imageoption'
        );

        return redirect()->route('verticals.index')
            ->with('success', 'Desarrollo vertical actualizado correctamente');
    }

    public function destroy($id)
    {
        return $this->deleteDevelopment($id);
    }

    public function destroyImage($developmentId, $imageId)
    {
        $development = Developments::findOrFail($developmentId);

        if ($this->deleteImage($development, $imageId)) {
            return redirect()->back()->with('success', 'Imagen eliminada correctamente');
        }

        return redirect()->back()->with('error', 'Imagen no encontrada');
    }
}
