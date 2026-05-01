<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// ShouldQueue removed: always sent synchronously via Mail::send().
class DynamicTemplateMail extends Mailable
{
    use SerializesModels;

    public array $compiled;

    public function __construct(array $compiled)
    {
        $this->compiled = $compiled;
    }

    public function build()
    {
        $mail = $this->subject($this->compiled['subject'])
            ->from($this->compiled['from_email'], $this->compiled['from_name'])
            ->view('emails.dynamic')
            ->with([
                'htmlContent' => $this->compiled['html'],
            ]);

        if (!empty($this->compiled['reply_to'])) {
            $mail->replyTo($this->compiled['reply_to']);
        }

        return $mail;
    }
}
