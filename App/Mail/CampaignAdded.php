<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $contactName;
    public $campaignName;

    public function __construct($contactName, $campaignName)
    {
        $this->contactName = $contactName;
        $this->campaignName = $campaignName;
    }

    public function build()
    {
        return $this->subject('Te hemos agregado a una campaña')
                    ->view('emails.campaign_added');  
    }
}
