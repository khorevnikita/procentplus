<?php

namespace App\Mail;

use App\MobileUser;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MobileUser $user,$token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.reset_password')->subject("Инструкции по восстановлению пароля");
    }
}
