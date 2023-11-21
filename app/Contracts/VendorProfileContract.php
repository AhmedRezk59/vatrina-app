<?php

namespace App\Contracts;

use App\Models\Vendor;

interface VendorProfileContract
{
    public function updateVendorInfo($request): Vendor;
    public function updatePassword($request): Vendor;
    public function updateAvatar($request): Vendor;    
}