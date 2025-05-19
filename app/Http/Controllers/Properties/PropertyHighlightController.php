<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\PropertiesHighlights;
use App\Traits\Web\HandlesHighlights;
use Illuminate\Http\Request;

class PropertyHighlightController extends Controller
{
    use HandlesHighlights;

    protected $modelHighlights = PropertiesHighlights::class;
    protected $inputID = 'id_property';
    protected $fieldID = 'id_property';

    public function index()
    {
        $viewState = 'admin.properties.highlights.index';
        return $this->indexHighlights($viewState);
    }

    public function store(Request $request)
    {
        return $this->storeHighlight($request);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
