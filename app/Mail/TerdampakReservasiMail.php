<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TerdampakReservasiMail extends Mailable
{
    use Queueable, SerializesModels;
    //menyimpan data yang akan ditampilkan
    public array $data;
        //menerima data yg akan ditampilakn
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subject
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Perubahan Jadwal Dokter - Klinik Gigi Dokter Ndeso',
        );
    }
    //isi dan view
    public function content(): Content
    {
        return new Content(
            view: 'emails.terdampak-reservasi',
            with: ['data' => $this->data]
        );
    }
}