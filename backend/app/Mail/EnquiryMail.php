<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subjectLine;
    public $role;

    public function __construct($data, $subjectLine = 'Thank you for your enquiry', $role)
    {
        $this->data = $data;
        $this->subjectLine = $subjectLine;
        $this->role = $role;
    }

    public function build(){

        $systemFromAddress = config('mail.from.address');
        $systemFromName    = config('mail.from.name');
        $adminEmail        = config('mail.admin_email', env('ADMIN_EMAIL','kishan@isearchsolution.com'));
        $customerEmail     = $this->data['email'];
        $customerName      = $this->data['name'] ?? 'Indian Panorama Enquiry';

        // Base setup (subject + bcc)
        $mail = $this->subject($this->subjectLine);
            // ->bcc(['nisha@isearchsolution.com']);

        if ($this->role == 'admin') {

            // Sent via Gmail SMTP, which rejects/rewrites a From address that
            // isn't the authenticated account (or a configured Send As alias) —
            // so this must go out from our own address, with the customer set
            // as Reply-To so a direct "Reply" in the inbox still reaches them.
            $mail->from($systemFromAddress, $systemFromName)
                ->replyTo($customerEmail, $customerName)
                ->view('emails.admin')
                ->with('data', $this->data);

        } else {
            $mail->from($systemFromAddress, $systemFromName)
                ->replyTo($adminEmail, $systemFromName)
                ->view('emails.enquiry')
                ->with('data', $this->data);
        }

        return $mail;
    }

}
