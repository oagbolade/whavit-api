<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DisinfectionMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $isUser = false;
    public $viewDirectory = 'emails.disinfection.whavitfeedback';
    public $subject = 'There is a New Enquiry for Disinfection Services';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userData, $isUser)
    {
        $this->userData = $userData;
        $this->isUser = $isUser;
        if ($isUser) {
            $this->viewDirectory = 'emails.disinfection.disinfectionrequest';
            $this->subject = 'Thank you for booking our disinfection service.';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view($this->viewDirectory)
            ->with([
                'data' => $this->userData,
            ]);
    }
}
