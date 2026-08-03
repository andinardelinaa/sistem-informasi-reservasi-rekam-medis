<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservasiRescheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; //me nyimpan data yg akan ditampilkan
    // menerima data
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subject email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi: Jadwal Reservasi Diperbarui',
        );
    }
    //isi dan view email
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservasi-reschedule',
            with: ['data' => $this->data]
        );
    }
}