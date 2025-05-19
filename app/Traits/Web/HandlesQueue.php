<?php

namespace App\Traits\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

trait HandlesQueue
{
    public function indexQueue($viewEstate)
    {
        $estates = $this->modelQueue::with(['estado', 'municipio', 'colonia'])
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

    public function storeQueue(Request $request, $viewEstate)
    {
        $estateQueue = $this->modelQueue::findOrFail($request->id);
        $newEstate = $this->model::create(Arr::except($estateQueue->toArray(), ['id']));

        $sourceDir = "storage/img/postsqueue/{$this->directory}/{$estateQueue->id}";
        $destinationDir = "storage/img/posts/{$this->directory}/{$newEstate->id}";

        File::ensureDirectoryExists(public_path($destinationDir), 0777, true);

        collect(File::allFiles(public_path($sourceDir)))
            ->each(fn($file) => File::move(
                $file->getRealPath(),
                public_path("$destinationDir/{$file->getFilename()}")
            ));

        File::deleteDirectory(public_path($sourceDir));
        $estateQueue->delete();

        return view($viewEstate);
    }
}
