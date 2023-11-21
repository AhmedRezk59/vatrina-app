<?php

namespace App\Listeners;

use App\Events\NewVendorRegistered;
use App\Services\SendWhatsappMessage;

class SendVendorWhatsappMessege
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewVendorRegistered $event): void
    { 
        (new SendWhatsappMessage($event->vendor, $event->text, $event->template))->send();
    }
}