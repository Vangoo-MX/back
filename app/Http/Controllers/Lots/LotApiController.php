<?php

namespace App\Http\Controllers\Lots;

use App\Http\Controllers\Controller;
use App\Models\Lots;
use App\Models\LotsHighlights;
use Illuminate\Http\Request;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;

class LotApiController extends Controller
{
    use HandlesHighlights, HandlesEstate;

    protected $model = Lots::class;
    protected $highlightModel = LotsHighlights::class;
    protected $highlightRelationship = 'lot';
    protected $directory = 'lots';
    protected $priceRangeColumns = [
        'min' => 'price_min',
        'max' => 'price_max'
    ];

    public function getLotsHightlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getLot($id)
    {
        return $this->getEstate($id);
    }

    public function getLotRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getLotsSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }
}
