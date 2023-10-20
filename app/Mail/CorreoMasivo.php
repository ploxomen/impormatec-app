<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorreoMasivo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $htmlText;
    public $documentos;

    public function __construct($html,$documentos)
    {
        $this->htmlText = $html;
        $this->documentos = $documentos;
    }
    // public function build()
    // {
    //     // return $this->view("administracion.correoPublicidad");
    //     // dd($envio);
    //     // foreach ($this->documentos as $documento) {
    //     //     $envio = $envio->attach($documento['url'],['as' => $documento['name']]);
    //     // }
    //     // dd($envio);
    //     // return $envio;
    // }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    // public function envelope()
    // {
    //     return new Envelope(
    //         subject: 'Correo Masivo',
    //     );
    // }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'administracion.correoPublicidad',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        // dd(Attachment::fromPath('/path/to/file'),$this->documentos);
        return [];
    }
}
