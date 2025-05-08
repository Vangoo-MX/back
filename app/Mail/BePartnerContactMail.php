<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BePartnerContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected array $formData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo formulario de contacto Vangoo'
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
        return implode("\n", [
            'Nuevo mensaje:',
            "Nombre: {$this->formData['name']}",
            "Email: {$this->formData['email']}",
            "Tel: {$this->formData['tel']}",
            "Asunto: {$this->formData['asunto']}",
            "Mensaje: {$this->formData['mensaje']}"
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
