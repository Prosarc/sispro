<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use App\SolicitudServicio;
use App\Personal;
use App\FirmasServicios;

class SolSerRM extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $pdfPath;
    public $firmas;
    public $cliente;
    public $GenerResiduos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $pdfPath, $firmas, $cliente, $GenerResiduos)
    {
        $this->email = $email;
        $this->firmas  = $firmas;
        $this->pdfPath = $pdfPath;
        $this->cliente = $cliente;
        $this->GenerResiduos = $GenerResiduos;

        //dd($GenerResiduos);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mensaje = $this->from('notificaciones@prosarc.com.co', 'Prosarc S.A. ESP');

        // Adjuntar el PDF existente usando la ruta del archivo
        $mensaje->attach($this->pdfPath, [
            'as' => 'Recibo de Materia Solicitud No. ' .$this->GenerResiduos->FK_SolSer .'.pdf', // Nombre del archivo adjunto
            'mime' => 'application/pdf',
        ]);

        $mensaje->markdown('emails.SolSer.ReciboMaterial')
                ->subject('Recibo de material generado para la solicitud No.' .$this->GenerResiduos->FK_SolSer);

        return $mensaje;
    }
}
