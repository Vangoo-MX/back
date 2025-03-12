<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldRejectedApartments extends Command
{
    protected $signature = 'apartments:cleanup';
    protected $description = 'Eliminar apartamentos rechazados con más de 3 meses de antigüedad';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subMonths(3);

        $deleted = DB::table('list_apartments_queue')
            ->where('status_aproved', 2)
            ->where('updated_at', '<=', $thresholdDate)
            ->delete();

        $this->info("Se eliminaron $deleted apartamentos rechazados.");
    }
}
