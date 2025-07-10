<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstadoPedidoActualizado extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $estado;

    public function __construct($pedido, $estado)
    {
        $this->pedido = $pedido;
        $this->estado = $estado;
    }

    public function build()
    {
        return $this->subject("Tu pedido ha sido actualizado a: $this->estado")
            ->view('emails.estado_pedido_actualizado'); // ✅ sí existe


    }
}
