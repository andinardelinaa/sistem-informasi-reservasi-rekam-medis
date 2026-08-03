<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; //menyimpan data yg akan ditampilkan
    //menerima data 
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subjek
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Anda - Klinik Gigi Dokter Ndeso',
        );
    }
    //view dan isi
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: ['data' => $this->data]
        );
    }
}