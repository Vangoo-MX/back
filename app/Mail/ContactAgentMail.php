<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAgentMail extends Mailable
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
        $content = "Una persona está interesada: \n";
        $content .= "Id propiedad: " . $this->formData['id_property'] . "\n";
        $content .= "Tipo: " . $this->formData['type_property'] . "\n";

        if ($this->formData['type_property'] === 'propiedad') {
            $content .= "Nombre de la propiedad: " . $this->formData['title_property'] . "\n";
        } elseif ($this->formData['type_property'] === 'desarrollo') {
            $content .= "Nombre del desarrollo: " . $this->formData['title_property'] . "\n";
        } elseif ($this->formData['type_property'] === 'lots') {
            $content .= "Nombre del lote: " . $this->formData['title_property'] . "\n";
        } else if ($this->formData['type_property'] === 'apartment') {
            $content .= "Nombre del apartamento: " . $this->formData['title_property'] . "\n";
        } else if ($this->formData['type_property'] === 'terrains') {
            $content .= "Nombre del terreno: " . $this->formData['title_property'] . "\n";
        }

        if ($this->formData['type_property'] === 'propiedad') {
            $content .= "Ubicacion de la propiedad: " . $this->formData['location_property'] . "\n";
        } elseif ($this->formData['type_property'] === 'desarrollo') {
            $content .= "Ubicacion del desarrollo: " . $this->formData['location_property'] . "\n";
        } elseif ($this->formData['type_property'] === 'lots') {
            $content .= "Ubicacion del lote: " . $this->formData['location_property'] . "\n";
        } else if ($this->formData['type_property'] === 'apartment') {
            $content .= "Ubicacion del apartamento: " . $this->formData['location_property'] . "\n";
        } else if ($this->formData['type_property'] === 'terrains') {
            $content .= "Ubicacion del terreno: " . $this->formData['location_property'] . "\n";
        }

        $content .= "Nombre: " . $this->formData['name'] . "\n";
        $content .= "Email: " . $this->formData['email'] . "\n";
        $content .= "Tel: " . $this->formData['tel'] . "\n";
        $content .= "Mensaje: " . $this->formData['msg'] . "\n";
        $content .= "Horario para contactar: " . $this->formData['horario'] . "\n";
        $content .= "Preferencia de contacto: " . $this->formData['pref_contact'] . "\n";

        return $this->subject('Nuevo formulario de contactar agente Vangoo')
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
            subject: 'Contact Agent Mail',
        );
    }

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
