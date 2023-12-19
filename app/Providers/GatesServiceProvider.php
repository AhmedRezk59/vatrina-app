<?php

namespace App\Providers;

use App\Models\Admin;
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

        Gate::define('admin-can-delete-himself', function (Admin $admin, Admin $admin2) {
            return $admin->is($admin2)
                ? Response::allow()
                : Response::denyWithStatus(403);
        });
        
        Gate::define('ensure-vendor-is-not-banned', function (Vendor $vendor) {
            return ! $vendor->is_banned
                ? Response::allow()
                : Response::denyWithStatus(403);
        });
    }
}