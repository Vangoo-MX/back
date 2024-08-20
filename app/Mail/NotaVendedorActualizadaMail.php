<?php

namespace App\Mail;

use App\Models\AgendaDocs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotaVendedorActualizadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agenda;
    public $vendedor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AgendaDocs $agenda)
    {
        $this->agenda = $agenda;
        $this->vendedor = $agenda->agendaRelation->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nota Vendedor Actualizada')
            ->markdown('emails.nota_actualizada')
            ->with([
                'agenda' => $this->agenda,
                'vendedor' => $this->vendedor,
            ]);
    }
}
