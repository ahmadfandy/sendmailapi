<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShgbMergerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataArray;

    /**
     * Create a new message instance.
     *
     * @param array $dataArray
     * @return void
     */
    public function __construct($dataArray)
    {
        $this->dataArray = $dataArray;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject($this->dataArray['subject'])
                    ->view('emails.shgbmerger.send')
                    ->with([
                        'dataArray' => $this->dataArray,
                    ]);
    }
}
