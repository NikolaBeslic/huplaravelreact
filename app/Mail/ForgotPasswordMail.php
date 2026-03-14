<?php

namespace App\Mail;

use App\Models\Korisnik;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $user;
    public $appName;
    public $logoUrl;


    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($resetUrl, Korisnik $user, $appName, $logoUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->user = $user;
        $this->appName = $appName;
        $this->logoUrl = $logoUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Resetovanje lozinke')
            ->view('emails.forgot-password');
    }
}
