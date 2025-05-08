<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAgentMail extends Mailable
{
    use Queueable, SerializesModels;

    protected const PROPERTY_TYPES = [
        'propiedad' => 'propiedad',
        'desarrollo' => 'desarrollo',
        'horizontalDev' => 'desarrollo horizontal',
        'lots' => 'lote',
        'apartment' => 'apartamento',
        'terrains' => 'terreno',
    ];

    public function __construct(
        protected array $formData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo formulario de contactar agente Vangoo'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: ['content' => $this->prepareEmailContent()]
        );
    }

    protected function prepareEmailContent(): string
    {
        $content = "Una persona está interesada: \n";
        $content .= "Id propiedad: {$this->formData['id_property']}\n";
        $content .= "Tipo: {$this->formData['type_property']}\n";

        if ($propertyType = $this->getTranslatedPropertyType()) {
            $content .= "Nombre del {$propertyType}: {$this->formData['title_property']}\n";
            $content .= "Ubicación del {$propertyType}: {$this->formData['location_property']}\n";
        }

        $content .= implode("\n", [
            "Nombre: {$this->formData['name']}",
            "Email: {$this->formData['email']}",
            "Tel: {$this->formData['tel']}",
            "Mensaje: {$this->formData['msg']}",
            "Horario para contactar: {$this->formData['horario']}",
            "Preferencia de contacto: {$this->formData['pref_contact']}"
        ]);

        return $content;
    }

    protected function getTranslatedPropertyType(): ?string
    {
        return self::PROPERTY_TYPES[$this->formData['type_property']] ?? null;
    }

    public function attachments()
    {
        return [];
    }
}
