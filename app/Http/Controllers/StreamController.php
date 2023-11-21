<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StreamController extends Controller
{
    public function getVendorAvatar(Vendor $vendor)
    {
        $path = Storage::disk('public')->path($vendor->avatar);

        if (file_exists($path)) {
            return response()->download($path, null, [], null);
        } else {
            throw new FileNotFoundException('This file doesn\'t exist');
        }
    }

    public function getUserAvatar(User $user)
    {
        $path = Storage::disk('public')->path($user->avatar);

        if (file_exists($path)) {
            return response()->download($path, null, [], null);
        } else {
            throw new FileNotFoundException('This file doesn\'t exist');
        }
    }
    
    public function getProductImage(Product $product)
    {
        $path = Storage::disk('public')->path($product->image);

        if (file_exists($path)) {
            return response()->download($path, null, [], null);
        } else {
            throw new FileNotFoundException('This file doesn\'t exist');
        }
    }
}