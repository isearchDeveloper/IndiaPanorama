<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $role;

    public function __construct(array $data, string $subjectLine, string $role)
    {
        $this->data        = $data;
        $this->subject     = $subjectLine;
        $this->role        = $role;
    }

    public function build(): self
    {
        $systemAddress = config('mail.from.address');
        $systemName    = config('mail.from.name');
        $adminEmail    = config('mail.admin_email', env('ADMIN_EMAIL', 'cholantoursenquiry@gmail.com'));

        $mail = $this->subject($this->subject);

        if ($this->role === 'admin') {
            $mail->from($systemAddress, $systemName)
                 ->replyTo($this->data['customer_email'], $this->data['customer_name'])
                 ->view('emails.booking_admin')
                 ->with('data', $this->data);
        } else {
            $mail->from($systemAddress, $systemName)
                 ->replyTo($adminEmail, $systemName)
                 ->view('emails.booking_customer')
                 ->with('data', $this->data);
        }

        return $mail;
    }
}
