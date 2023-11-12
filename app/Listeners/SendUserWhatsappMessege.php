<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Notifications\SendWhatsappMessegeNotification;
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
        $event->user->notify(new SendWhatsappMessegeNotification($event->user->phone_number, $event->template ,$event->text));
    }
}