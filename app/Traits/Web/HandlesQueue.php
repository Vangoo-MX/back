<?php

namespace App\Traits\Web;

trait HandlesQueue
{
    public function indexQueue($viewEstate)
    {
        $estates = $this->model::with(['estado', 'municipio', 'colonia'])
            ->whereIn('status_aproved', [0, 2, 3])
            ->get();

        $estatesQueue = $estates->where('status_aproved', 0)->values();
        $estatesRejected = $estates->where('status_aproved', 2)->values();
        $estatesRevision = $estates->where('status_aproved', 3)->values();

        return view($viewEstate, compact(
            'estatesQueue',
            'estatesRejected',
            'estatesRevision'
        ));
    }
}
