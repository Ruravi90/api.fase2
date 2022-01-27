<?php

namespace fase2\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class ExecJobCron extends Mailable
{
    use Queueable, SerializesModels;
    public $time;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($time = null)
    {
        if($time == null){
            $time =Carbon::now();
        }

        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.jobcron');
    }
}
