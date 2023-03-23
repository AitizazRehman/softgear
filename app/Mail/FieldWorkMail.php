<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FieldWorkMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $attendance;
    public $user;
    public $hrs;
    public function __construct($attendance, $user, $hrs)
    {
        $this->attendance = $attendance;
        $this->user = $user;
        $this->hrs = $hrs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.field-work')
                    ->subject('Field Work Reminder');
    }
}
