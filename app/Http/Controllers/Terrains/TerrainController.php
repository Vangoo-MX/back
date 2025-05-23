<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class TerrainController extends Controller
{
    use HandlesEstate;

    protected $model = Terrains::class;
    protected $highlightModel = TerrainsHighlights::class;
    protected $directory = 'terrains';

    public function index()
    {
        $viewEstate = 'admin.terrains.index';
        return $this->indexEstate($viewEstate);
    }

    public function show($id)
    {
        $viewEstate = 'admin.terrains.show';
        return $this->showEstate($id, $viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.terrains.edit';
        return $this->editEstate($id, $viewEstate);
    }
}
