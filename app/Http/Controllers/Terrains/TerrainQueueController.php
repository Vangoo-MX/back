<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsQueue;
use App\Traits\Web\HandlesQueue;
use Illuminate\Http\Request;

class TerrainQueueController extends Controller
{
    use HandlesQueue;

    protected $model = Terrains::class;
    protected $modelQueue = TerrainsQueue::class;
    protected $directory = 'terrains';

    public function index()
    {
        $viewEstate = 'admin.terrains.queue.index';
        return $this->indexQueue($viewEstate);
    }

    public function store(Request $request)
    {
        return $this->approvedQueue($request);
    }

    public function update($id)
    {
        return $this->revisionQueue($id);
    }

    public function reject($id)
    {
        return $this->rejectQueue($id);
    }
}
