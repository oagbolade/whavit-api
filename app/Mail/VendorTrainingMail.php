<?php

namespace App\Mail;

use App\User;
use App\VendorTraining;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VendorTrainingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $vendorTraining;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, VendorTraining $vendorTraining)
    {
        $this->user = $user;
        $this->vendorTraining = $vendorTraining;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Training Date Is Set!')->view('emails.vendor.training')
                                                    ->with([
                                                        'first_name' => $this->user->first_name,
                                                        'last_name' => $this->user->last_name,
                                                        'training_date' => $this->vendorTraining->training_date,
                                                        'training_time' => $this->vendorTraining->training_time
                                                    ]);
    }
}
