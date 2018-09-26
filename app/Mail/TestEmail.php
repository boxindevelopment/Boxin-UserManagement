<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;

    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address    = $this->data['from'];
        $subject    = $this->data['subject'];
        $name       = $this->data['name_from'];
        
        return $this->view($this->data['view'])
                    ->from($address, $name)
                    ->subject($subject)
                    ->with([ 
                        'email'     => $this->data['email'],
                        'password'  => $this->data['password'], 
                        'first_name'=> $this->data['name_from'],
                        'last_name' => $this->data['last_name'],
                        'phone'     => $this->data['phone'],
                        'question'  => $this->data['question'],
                        'subject'   => $subject,
                    ]);
    }
}
