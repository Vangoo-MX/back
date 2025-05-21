<?php

namespace App\Http\Controllers\Apartments;

use App\Http\Controllers\Controller;
use App\Traits\Web\HandlesEstate;
use App\Models\ApartmentsHighlights;
use App\Models\Apartments;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    use HandlesEstate;
    protected $model = Apartments::class;
    protected $highlightModel = ApartmentsHighlights::class;
    protected $directory = 'apartments';

    public function index()
    {
        $viewEstate = 'admin.apartments.index';
        return $this->indexEstate($viewEstate);
    }

    public function show($id)
    {
        $viewEstate = 'admin.apartments.show';
        return $this->showEstate($id, $viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.apartments.edit';
        return $this->editEstate($id, $viewEstate);
    }
}
