<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertiesHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class PropertyHighlightController extends Controller
{
    use HandlesHighlights;

    protected $model = Properties::class;
    protected $modelHighlights = PropertiesHighlights::class;
    protected $relationHighlight = 'property';
    protected $relationMunicipio = 'properties';

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_property',
            'field_id' => 'id_property',
        ];
    }

    public function index()
    {
        $viewState = 'admin.properties.highlights.index';
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

    public function propertyByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
