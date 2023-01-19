<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AceiteUsadoProgramado extends Mailable implements ShouldQueue
{
    use Queueable;

    public $email;
    public $SolicitudServicio;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $SolicitudServicio)
    {
        $this->email = $email;
        $this->SolicitudServicio = $SolicitudServicio;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('notificaciones@prosarc.com.co', $this->email->CliName)
                    ->subject('Aceites Usados en el Servicio '.'#'.$this->SolicitudServicio->ID_SolSer.' del cliente: '.$this->SolicitudServicio['cliente']->CliName)
                    ->markdown('emails.SolSer.AceiteUsadoProgramado');
    }
}
