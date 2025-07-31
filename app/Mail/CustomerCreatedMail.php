<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $customerName;
    public $accountName;
    public $temporaryPassword;
    public $companyName;

    /**
     * Create a new message instance.
     *
     * @param string $customerName
     * @param string $accountName
     * @param string $temporaryPassword
     * @param string $companyName
     * @return void
     */
    public function __construct($customerName, $accountName, $temporaryPassword, $companyName)
    {
        $this->customerName = $customerName;
        $this->accountName = $accountName;
        $this->temporaryPassword = $temporaryPassword;
        $this->companyName = $companyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.customer_created')
            ->subject('Tài khoản của bạn đã được tạo thành công')
            ->with([
                'customerName' => $this->customerName,
                'accountName' => $this->accountName,
                'temporaryPassword' => $this->temporaryPassword,
                'companyName' => $this->companyName,
            ]);
    }
}
