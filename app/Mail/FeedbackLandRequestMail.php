<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackLandRequestMail extends Mailable
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
        return $this->subject('FYI : Payment Request for  '.$this->mailData['doc_no'].' has been approved')
                    ->view('emails.feedback.landrequest')
                    ->with(['data' => $this->mailData]);
    }
}
