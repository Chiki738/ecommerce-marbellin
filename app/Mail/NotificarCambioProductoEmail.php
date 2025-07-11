<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificarCambioProductoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $cambio;
    public $mensaje;

    /**
     * Create a new message instance.
     */
    public function __construct($cambio, $mensaje)
    {
        $this->cambio = $cambio;
        $this->mensaje = $mensaje;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación sobre tu cambio de producto'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notificar-cambio',
            with: [
                'cambio' => $this->cambio,
                'mensaje' => $this->mensaje
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
