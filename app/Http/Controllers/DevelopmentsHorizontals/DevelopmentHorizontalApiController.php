<?php

namespace App\Http\Controllers\DevelopmentsHorizontals;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;
use Illuminate\Http\Request;

class DevelopmentHorizontalApiController extends Controller
{
    use HandlesHighlights, HandlesEstate;

    protected $model = DevelopmentsHorizontals::class;
    protected $highlightModel = DevelopmentsHorizontalHighlights::class;
    protected $highlightRelationship = 'horizontal';
    protected $directory = 'developmentsHorizontal';
    protected $priceRangeColumns = [
        'min' => 'price_min',
        'max' => 'price_max'
    ];

    public function getDevelopmentsHorizontalHighlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getDevelopmentHorizontal($id)
    {
        return $this->getEstate($id);
    }

    public function getDevelopmentHorizontalFavorites($id)
    {
        return $this->getEstateFavorites($id);
    }

    public function getDevelopmentHorizontalRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getDevelopmentsHorizontalSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }
}
