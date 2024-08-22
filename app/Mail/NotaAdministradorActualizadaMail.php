<?php

namespace App\Mail;

use App\Models\AgendaDocs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotaAdministradorActualizadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agenda;
    public $admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AgendaDocs $agenda)
    {
        $this->agenda = $agenda;
        $this->admin = $agenda->agendaRelation->user->name;
    }

    public function build()
    {
        return $this->subject('Nota Administrador Actualizada')
            ->markdown('emails.nota_administrador_actualizada')
            ->with([
                'agenda' => $this->agenda,
                'admin' => $this->admin,
            ]);
    }
}
