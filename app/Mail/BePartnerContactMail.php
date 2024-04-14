<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BePartnerContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function build()
    {
        $content = "Nuevo mensaje: \n";
        $content .= "User: " . ($this->formData['id_user'] ? $this->formData['id_user'] : 'No') . "\n";
        $content .= "Nombre: " . $this->formData['name'] . "\n";
        $content .= "Email: " . $this->formData['email'] . "\n";
        $content .= "Tel: " . $this->formData['tel'] . "\n";
        $content .= "Asunto: " . $this->formData['asunto'] . "\n";
        $content .= "Mensaje: " . $this->formData['mensaje'] . "\n";

        return $this->subject('Nuevo formulario de contacto Vangoo')
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
            subject: 'Be Partner Contact Mail',
        );
    }

    // /**
    //  * Get the message content definition.
    //  *
    //  * @return \Illuminate\Mail\Mailables\Content
    //  */
    // public function content()
    // {
    //     return new Content(
    //         view: 'emails.confirm',
    //     );
    // }

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
