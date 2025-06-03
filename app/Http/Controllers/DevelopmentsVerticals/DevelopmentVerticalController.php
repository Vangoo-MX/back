<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\Developments;
use App\Models\DevelopmentsApartments;
use App\Models\DevelopmentsHighlights;
use App\Traits\Web\HandlesEstate;
use App\Traits\Utility\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevelopmentVerticalController extends Controller
{
    use HandlesEstate, HandlesImage;
    protected $model = Developments::class;
    protected $highlightModel = DevelopmentsHighlights::class;
    protected $apartmentModel = DevelopmentsApartments::class;
    protected $directory = 'developments';
    protected $basePath = 'posts';

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
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'area' => $request->area,
            'amenities' => $request->amenities,
            'commission_percentage' => $request->commission_percentage,
            'id_user' => Auth::user()->id,
        ]);

        $this->handleImageProcessing($request, $development);
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
            'cp',
            'street',
            'num_ext',
            'map_lat',
            'map_long',
            'area',
            'amenities',
            'commission_percentage'
        ]);

        $this->handleImageProcessing($request, $development);

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

    public function reorderImages(Request $request, Developments $vertical)
    {
        return $this->reorderEstateImages($request, $vertical);
    }

    public function deleteImage(Developments $vertical, $filename)
    {
        return $this->destroyImage($vertical, $filename);
    }
}
