<?php

namespace App\Http\Controllers\Lots;

use App\Http\Controllers\Controller;
use App\Models\Lots;
use App\Models\LotsHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class LotController extends Controller
{
    use HandlesEstate;
    protected $model = Lots::class;
    protected $highlightModel = LotsHighlights::class;
    protected $directory = 'lots';

    public function index()
    {
        $viewEstate = 'admin.lots.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.lots.create';
        return $this->createEstate($viewEstate);
    }
}
