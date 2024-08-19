<?php

namespace App\Listeners;

use App\Events\NotaVendedorActualizada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotaVendedorActualizadaMail;

class EnviarCorreoNotaVendedor
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\NotaVendedorActualizada  $event
     * @return void
     */
    public function handle(NotaVendedorActualizada $event)
    {
        $agenda = $event->agenda;
        $admin = $agenda->agendaRelation->user;
        Mail::to($admin->email)->send(new NotaVendedorActualizadaMail($agenda));
    }
}
