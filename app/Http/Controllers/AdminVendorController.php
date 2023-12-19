<?php

namespace App\Http\Controllers;

use App\Models\Vendor;

class AdminVendorController extends Controller
{
    public function banVendor(Vendor $vendor)
    {
        $vendor->update([
            'is_banned' => true
        ]);

        info("Vendor {$vendor->username} got banned successfully");

        return $this->apiResponse(
            msg: "Vendor {$vendor->username} got banned successfully"
        );
    }
}