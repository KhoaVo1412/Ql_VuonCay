<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;
    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($resetLink, $userName)
    {
        $this->resetLink = $resetLink;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Liên kết đổi mật khẩu của bạn')
            ->view('emails.forgot_password_plain') // Chỉ cần định nghĩa view tại đây
            ->with('resetLink', $this->resetLink);
    }
}
