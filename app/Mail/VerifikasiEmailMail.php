<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiEmailMail extends Mailable
{
    use Queueable, SerializesModels;
    //menyimpan data yg akan ditampilkan
    public array $data;
    //menerima data
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subjek
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email Anda - Klinik Gigi Dokter Ndeso',
        );
    }
    //isi dan view
    public function content(): Content
    {
        return new Content(
            view: 'emails.verifikasi-email',
            with: ['data' => $this->data]
        );
    }  
}