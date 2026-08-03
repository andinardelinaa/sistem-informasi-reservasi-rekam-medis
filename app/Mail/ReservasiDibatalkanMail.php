<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservasiDibatalkanMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; //menyimpan semua data yg akan ditampilkan
    // menerima data
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subjek email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi: Reservasi Dibatalkan',
        );
    }
    //isi dan view email
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservasi-dibatalkan',
            with: ['data' => $this->data]
        );
    }
}