<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserVerificationEmail
{
    /**
     * Handle the event.
     */
    public function handle(NewUserRegistered $event): void
    {
        if ($event->user instanceof MustVerifyEmail && !$event->user->hasVerifiedEmail()) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}