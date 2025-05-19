<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Traits\Web\HandlesQueue;
use Illuminate\Http\Request;

class PropertyQueueController extends Controller
{
    use HandlesQueue;

    protected $model = Properties::class;
    protected $modelQueue = PropertiesQueue::class;
    protected $directory = 'properties';

    public function index()
    {
        $viewEstate = 'admin.properties.queue.index';
        return $this->indexQueue($viewEstate);
    }

    public function store(Request $request)
    {
        return $this->approvedQueueQueue($request);
    }

    public function update($id)
    {
        return $this->revisionQueue($id);
    }
}
