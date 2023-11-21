<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Vendor;

class SendMessageToVendor
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(Vendor $vendor): array|bool
    {
        return ! $vendor->is_banned;
    }
}