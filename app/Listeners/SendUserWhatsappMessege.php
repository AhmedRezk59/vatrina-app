<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Notifications\SendWhatsappMessegeNotification;
use App\Services\SendWhatsappMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserWhatsappMessege
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
    public function handle(NewUserRegistered $event): void
    { 
        (new SendWhatsappMessage($event->user, $event->text, $event->template))->send();
    }
}