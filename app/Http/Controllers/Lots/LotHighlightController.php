<?php

namespace App\Http\Controllers\Lots;

use App\Http\Controllers\Controller;
use App\Models\Lots;
use App\Models\LotsHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class LotHighlightController extends Controller
{
    use HandlesHighlights;
    protected $model = Lots::class;
    protected $modelHighlights = LotsHighlights::class;
    protected $relationHighlight = 'lot';
    protected $relationMunicipio = 'lots';

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_lot',
            'field_id' => 'id_lot',
        ];
    }

    public function index()
    {
        $viewState = 'admin.lots.highlights.index';
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

    public function lotsByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
