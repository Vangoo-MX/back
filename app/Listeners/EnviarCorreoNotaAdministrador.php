<?php

namespace App\Listeners;

use App\Events\NotaAdministradorActualizada;
use App\Mail\NotaAdministradorActualizadaMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoNotaAdministrador
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\NotaAdministradorActualizada  $event
     * @return void
     */
    public function handle(NotaAdministradorActualizada $event)
    {
        $agenda = $event->agenda;
        $vendedor = $agenda->agendaRelation->email;
        Mail::to($vendedor)->send(new NotaAdministradorActualizadaMail($agenda));
    }
}
