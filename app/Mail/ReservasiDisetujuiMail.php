<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservasiDisetujuiMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; //menyimpan data yang akan ditampilkan
    //menerima data
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //subjek email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi: Reservasi Dilanjutkan dengan Dokter Pengganti',
        );
    }
    // view dan isi email
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservasi-disetujui',
            with: ['data' => $this->data]
        );
    }
}