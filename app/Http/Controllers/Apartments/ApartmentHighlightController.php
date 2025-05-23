<?php

namespace App\Http\Controllers\Apartments;

use App\Http\Controllers\Controller;
use App\Models\Apartments;
use App\Models\ApartmentsHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class ApartmentHighlightController extends Controller
{
    use HandlesHighlights;
    protected $model = Apartments::class;
    protected $modelHighlights = ApartmentsHighlights::class;

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_property',
            'field_id' => 'id_property',
        ];
    }

    public function index()
    {
        $viewState = 'admin.apartments.highlights.index';
        return $this->indexHighlights($viewState);
    }

    public function store(Request $request)
    {
        return $this->storeHighlight($request);
    }

    public function update(Request $request)
    {
        return $this->orderHightlight($request);
    }

    public function destroy($id)
    {
        return $this->deleteHighlight($id);
    }

    public function apartmentByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
