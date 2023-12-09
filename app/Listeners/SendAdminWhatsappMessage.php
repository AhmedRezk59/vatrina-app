<?php

namespace App\Listeners;

use App\Events\NewAdminRegistered;
use App\Services\SendWhatsappMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminWhatsappMessage
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
    public function handle(NewAdminRegistered $event): void
    {
        (new SendWhatsappMessage($event->admin, $event->text, $event->template))->send();
    }
}
