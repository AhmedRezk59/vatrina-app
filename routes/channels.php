<?php

use App\Broadcasting\SendMessageToUser;
use App\Broadcasting\SendMessageToVendor;
use App\Models\Vendor;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('sendMessageToVendor.{vendor}',SendMessageToVendor::class, ['guards' => "api"]);
Broadcast::channel('sendMessageToUser.{user}',SendMessageToUser::class, ['guards' => "api-vendor"]);