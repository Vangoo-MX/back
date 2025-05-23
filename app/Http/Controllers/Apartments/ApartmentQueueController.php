<?php

namespace App\Http\Controllers\Apartments;

use App\Http\Controllers\Controller;
use App\Models\Apartments;
use App\Models\ApartmentsQueue;
use App\Traits\Web\HandlesQueue;
use Illuminate\Http\Request;

class ApartmentQueueController extends Controller
{
    use HandlesQueue;

    protected $model = Apartments::class;
    protected $modelQueue = ApartmentsQueue::class;
    protected $directory = 'apartments';

    public function index()
    {
        $viewEstate = 'admin.apartments.queue.index';
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
