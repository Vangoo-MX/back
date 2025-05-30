<?php

namespace App\Http\Controllers\DevelopmentsHorizontals;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class DevelopmentHorizontalHighlightController extends Controller
{
    use HandlesHighlights;

    protected $model = DevelopmentsHorizontals::class;
    protected $modelHighlights = DevelopmentsHorizontalHighlights::class;
    protected $relationHighlight = 'developmentHorizontal';
    protected $relationMunicipio = 'developmentHorizontal';

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_development',
            'field_id' => 'id_development',
        ];
    }

    public function index()
    {
        $viewState = 'admin.horizontals.highlights.index';
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

    public function horizontalByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
