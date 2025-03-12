<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldRejectedProperties extends Command
{
    protected $signature = 'properties:cleanup';

    protected $description = 'Eliminar propiedades rechazadas que tengan más de 3 meses de antigüedad';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subMonths(3);

        $deleted = DB::table('list_properties_queue')
            ->where('satatus_aproved', 2)
            ->where('updated_at', '<=', $thresholdDate)
            ->delete();

        $this->info("Se eliminaron $deleted propiedades rechazadas");
    }
}
