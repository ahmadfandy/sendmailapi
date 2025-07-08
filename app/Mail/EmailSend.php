<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailSend extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     * 
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     * 
     * @return $this
     * 
     */
    public function build()
    {
        // $subject = $this->mailData['subject'];
		// $replyToName = $this->mailData['full_name'];
		// $replyTo = $this->mailData['email'];
        // $token  = $this->mailData['token'];

        // return $this->view('emails.kirim')
        //     ->cc($replyTo, $replyToName)
        //     ->subject($subject)
        //     ->replyTo($replyTo, $replyToName)
        //     ->with(['data' => $this->mailData]);
        return $this->subject('Need Approval')
                    ->view('emails.statis')
                    ->with(['data' => $this->mailData]);
    }
}
