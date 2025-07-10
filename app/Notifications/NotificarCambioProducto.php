<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificarCambioProducto extends Notification
{
    use Queueable;

    protected $cambio;
    protected $mensaje;

    public function __construct($cambio, $mensaje)
    {
        $this->cambio = $cambio;
        $this->mensaje = $mensaje;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Estado de tu solicitud de cambio de producto')
            ->greeting('Hola ' . $notifiable->nombre . ',')
            ->line('Tu solicitud de cambio #' . $this->cambio->id . ' ha sido procesada.')
            ->line($this->mensaje)
            ->line('Gracias por confiar en nosotros.');
    }
}
