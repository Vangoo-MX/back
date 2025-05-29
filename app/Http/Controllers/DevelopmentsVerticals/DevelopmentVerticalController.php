<?php

namespace App\Http\Controllers\DevelopmentsVerticals;

use App\Http\Controllers\Controller;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class DevelopmentVerticalController extends Controller
{
    use HandlesEstate;
    protected $model = Developments::class;
    protected $highlightModel = DevelopmentsHighlights::class;
    protected $directory = 'developments';

    public function index()
    {
        $viewEstate = 'admin.verticals.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.verticals.create';
        return $this->createEstate($viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.verticals.edit';
        return $this->editEstate($id, $viewEstate);
    }
}
