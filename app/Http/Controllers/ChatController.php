<?php

namespace App\Http\Controllers;

use App\Events\SendMessageToUser;
use App\Events\SendVendorAMessage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendVendorAMessage(Request $request, Vendor $vendor)
    {
        $request->validate([
            'msg' => ['required', 'string', 'max:500', 'min:1']
        ]);
        
        broadcast(new SendVendorAMessage($vendor, $request->msg));
        
        return $this->apiResponse(
            msg: "Message got sent to vendor " . $vendor->username,
        );
    }

    public function sendUserAMessage(Request $request, User $user)
    {
        $request->validate([
            'msg' => ['required', 'string', 'max:500', 'min:1']
        ]);

        broadcast(new SendMessageToUser($user, $request->msg));

        return $this->apiResponse(
            msg: "Message got sent to user " . $user->username,
        );
    }
}