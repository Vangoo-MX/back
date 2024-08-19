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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AgendaDocs $agenda)
    {
        $this->agenda = $agenda;
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
            ->with('agenda', $this->agenda);
    }
}
