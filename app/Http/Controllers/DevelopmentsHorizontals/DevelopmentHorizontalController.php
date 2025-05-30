<?php

namespace App\Http\Controllers\DevelopmentsHorizontals;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentsHorizontalApartments;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class DevelopmentHorizontalController extends Controller
{
    use HandlesEstate;

    protected $model = DevelopmentsHorizontals::class;
    protected $highlightModel = DevelopmentsHorizontalHighlights::class;
    protected $apartmentModel = DevelopmentsHorizontalApartments::class;
    protected $directory = 'developmentsHorizontal';

    public function index()
    {
        $viewEstate = 'admin.horizontals.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.horizontals.create';
        return $this->createEstate($viewEstate);
    }
}
