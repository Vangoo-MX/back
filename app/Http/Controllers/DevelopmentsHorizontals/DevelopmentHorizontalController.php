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
        return $this->editDevelopment($id, $viewEstate);
    }

    public function update(Request $request, $id)
    {
        $development = DevelopmentsHorizontals::findOrFail($id);

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
            'cp',
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

        return redirect()->route('horizontals.index')
            ->with('success', 'Desarrollo horizontal actualizado correctamente');
    }

    public function destroy($id)
    {
        return $this->deleteDevelopment($id);
    }

    public function destroyImage($developmentId, $imageId)
    {
        $development = DevelopmentsHorizontals::findOrFail($developmentId);

        if ($this->deleteImage($development, $imageId)) {
            return redirect()->back()->with('success', 'Imagen eliminada correctamente');
        }

        return redirect()->back()->with('error', 'Error al eliminar la imagen');
    }
}
