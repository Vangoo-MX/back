<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalesAdvisorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function build()
    {
        $content = "Nuevo mensaje: \n";
        $content .= "Nombre: " . $this->formData['name'] . "\n";
        $content .= "Email: " . $this->formData['email'] . "\n";
        $content .= "Tel: " . $this->formData['tel'] . "\n";
        $content .= "Asunto: " . $this->formData['asunto'] . "\n";
        $content .= "Mensaje: " . $this->formData['msg'] . "\n";
        $content .= "Horario para contactar: " . $this->formData['horario'] . "\n";
        $content .= "Preferencia de contacto: " . $this->formData['pref_contact'] . "\n";

        return $this->subject('Nueva solicitud de informacion')
            ->view('emails.template')
            ->with('content', $content);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Sales Advisor Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
