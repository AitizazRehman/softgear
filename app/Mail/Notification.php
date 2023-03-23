<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data.
     *
     * @var array
     */
    public $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename, $data)
    {
        $this->filename = $filename;
        $this->data = $data;
        // dd($this->data['description']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd(auth()->user());
        $user = auth()->user();
        // dd($user->email);
        return $this->view('email.attendance-report', [
            'email_data' => $this->data
        ])  ->from('aitizaz_cl@yahoo.com', $user->first_name)
            ->subject($this->data['subject'])
            ->cc($this->data['cc'] ?? '')
            ->attach($this->filename);
    }
}
