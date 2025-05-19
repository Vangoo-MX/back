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

    public function index()
    {
        $viewState = 'admin.properties.highlights.index';
        return $this->indexHighlights($viewState);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
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
