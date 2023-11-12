<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GatesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('vendor-owns-product', function (Vendor $vendor, Product $product) {
            return $product->vendor_id == $vendor->id
                ? Response::allow()
                : Response::denyWithStatus(403);
        });
    }
}
