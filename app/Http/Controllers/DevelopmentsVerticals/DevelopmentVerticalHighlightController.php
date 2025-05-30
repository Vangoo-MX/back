<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class DevelopmentVerticalHighlightController extends Controller
{
    use HandlesHighlights;
    protected $model = Developments::class;
    protected $modelHighlights = DevelopmentsHighlights::class;
    protected $relationHighlight = 'developmentVertical';
    protected $relationMunicipio = 'developmentVertical';

    protected function getHighlightConfig(): array
    {
        return [
            'input_id' => 'id_development',
            'field_id' => 'id_development',
        ];
    }

    public function index()
    {
        $viewState = 'admin.verticals.highlights.index';
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

    public function verticalByMunicipio($id)
    {
        return $this->estatesByMunicipio($id);
    }
}
