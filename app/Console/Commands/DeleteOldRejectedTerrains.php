<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldRejectedTerrains extends Command
{
    protected $signature = 'terrains:cleanup';

    protected $description = 'Eliminar terrenos rechazados que tengan más de 3 meses de antigüedad';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subMonths(3);

        $deleted = DB::table('list_terrains_queue')
            ->where('status_aproved', 2)
            ->where('updated_at', '<=', $thresholdDate)
            ->delete();

        $this->info("Se eliminaron $deleted terrenos rechazados");
    }
}
