<?php

namespace App\Listeners;

use App\Events\NewVendorRegistered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVendorVerificationEmail
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
        if ($event->vendor instanceof MustVerifyEmail && !$event->vendor->hasVerifiedEmail()) {
            $event->vendor->sendEmailVerificationNotification();
        }
    }
}