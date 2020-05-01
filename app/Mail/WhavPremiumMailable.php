<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WhavPremiumMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $type;
    public $isUser = false;
    public $viewDirectory = 'emails.WhavPremium.whavitfeedback';
    public $subject = 'There is a New Enquiry for WhavPremium Services';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type, $userData, $isUser)
    {
        $this->userData = $userData;
        $this->isUser = $isUser;
        $this->type = $type;
        if($isUser){
            $this->viewDirectory = 'emails.WhavPremium.whavpremiumrequest';
            $this->subject = 'More Information on the '. $type .' you Requested.';
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
            'type' => $this->type,
        ]);
    }
}
