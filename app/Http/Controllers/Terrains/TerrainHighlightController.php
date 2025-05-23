<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class TerrainHighlightController extends Controller
{
    use HandlesHighlights;

    protected $model = Terrains::class;
    protected $modelHighlights = TerrainsHighlights::class;
    protected $relationHighlight = 'terrain';
    protected $relationMunicipio = 'terrains';

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_property',
            'field_id' => 'id_property',
        ];
    }

    public function index()
    {
        $viewState = 'admin.terrains.highlights.index';
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

    public function terrainByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
