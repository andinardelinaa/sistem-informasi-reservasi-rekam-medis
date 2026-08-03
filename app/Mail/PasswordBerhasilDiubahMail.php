<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordBerhasilDiubahMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; //menyimpan seluruh data yang akan ditampilkan di email
    // menrima data dr controller atau service
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //menentukan subjek email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Berhasil Diubah - Klinik Gigi Dokter Ndeso',
        );
    }
    //menentukan isi email
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-berhasil-diubah',
            with: ['data' => $this->data]
        );
    }
}