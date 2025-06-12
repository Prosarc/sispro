<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use App\SolicitudServicio;
use App\Personal;

class SolserAuditar extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $AsuntoAuditoria;
    public $SolicitudServicio;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($SolicitudServicio)
    {
        $this->SolicitudServicio = $SolicitudServicio;

        
    }   

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
    switch ($this->SolicitudServicio->SolResAuditoriaTipo) {
        case "Virtual":
            $AsuntoAuditoria = 'El Servicio #'.$this-> SolicitudServicio->ID_SolSer.$this-> SolicitudServicio->CliName." Se Solicito Auditoría Virtual";
            break;
        case "Presencial":
            $AsuntoAuditoria = 'En el Servicio #'.$this-> SolicitudServicio->ID_SolSer.$this-> SolicitudServicio->CliName." Se Solicito Auditoría Presencial";
            break;
        default:
             $AsuntoAuditoria = 'El Servicio NO es auditado';
            break;    
    }
    return $this->from('notificaciones@prosarc.com.co', 'Prosarc S.A. ESP')
                    ->subject($AsuntoAuditoria)
                    ->markdown('emails.SolSer.AuditarEmail');
    }

}
