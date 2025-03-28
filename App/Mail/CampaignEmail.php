<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $contact;

    // Constructor para recibir la campaña y el contacto
    public function __construct(Campaign $campaign, Contact $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
    }

    // Construir el mensaje del correo
    public function build()
    {
        return $this->subject('Campaña de Marketing: ' . $this->campaign->name)
                    ->view('emails.campaign')
                    ->with([
                        'name' => $this->contact->name, 
                        'campaignName' => $this->campaign->name, 
                        'campaignDescription' => $this->campaign->description, 
                    ]);
    }
}
