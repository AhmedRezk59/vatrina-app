<?php

namespace App\Listeners;

use App\Events\NewVendorRegistered;
use App\Notifications\SendWhatsappMessegeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $event->vendor->notify(new SendWhatsappMessegeNotification($event->vendor->phone_number, $event->template, $event->text));
    }
}