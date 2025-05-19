<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\PropertiesQueue;
use App\Traits\Web\HandlesQueue;
use Illuminate\Http\Request;

class PropertyQueueController extends Controller
{
    use HandlesQueue;

    protected $model = PropertiesQueue::class;

    public function index()
    {
        $viewEstate = 'admin.properties.queue.index';
        return $this->indexQueue($viewEstate);
    }
}
