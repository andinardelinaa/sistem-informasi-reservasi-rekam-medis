<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoCancelMail extends Mailable
{ 
    use Queueable, SerializesModels;

    public array $data; //menampung selurh data
    //menerima data dari controller atau service
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    //menyusun email
    public function build(): self
    {
        return $this
            ->subject('Reservasi Dibatalkan Otomatis - ' . ($this->data['detail_reservasi']['pasien'] ?? ''))
            ->view('emails.auto-cancel')
            ->with($this->data);
    }
}