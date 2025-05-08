<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;
use Illuminate\Http\Request;

class DevelopmentVerticalApiController extends Controller
{
    use HandlesHighlights, HandlesEstate;

    protected $model = Developments::class;
    protected $highlightModel = DevelopmentsHighlights::class;
    protected $highlightRelationship = 'developmentVertical';
    protected $directory = 'developments';
    protected $priceRangeColumns = [
        'min' => 'price_min',
        'max' => 'price_max'
    ];

    public function getDevelopmentsVerticalHighlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getDevelopmentVertical($id)
    {
        return $this->getEstate($id);
    }

    public function getDevelopmentVerticalRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getDevelopmentsVerticalSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }
}
