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
        $viewEstate = 'admin.developments.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.developments.create';
        return $this->createEstate($viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.developments.edit';
        return $this->editEstate($id, $viewEstate);
    }
}
