<?php

namespace App\Providers;

use App\Contracts\AdminContract;
use App\Contracts\AdminControllerInterface;
use App\Contracts\CollectionContract;
use App\Contracts\GetCollectionsInterface;
use App\Contracts\GetProductsInterface;
use App\Contracts\OrderControllerRepositoryInterface;
use App\Contracts\ProductContract;
use App\Contracts\ProductControllerContract;
use App\Contracts\VendorProfileContract;
use App\Repositories\AdminControllerRepository;
use App\Repositories\FilterForAdminContract;
use App\Repositories\FilterForCollectionContract;
use App\Repositories\FilterForProductContract;
use App\Repositories\GetCollectionsRepository;
use App\Repositories\GetProductsRepository;
use App\Repositories\OrderControllerRepository;
use App\Repositories\ProductControllerRepository;
use App\Repositories\VendorProfileRepository;
use App\Services\CalculateProductsFinalPriceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CollectionContract::class, FilterForCollectionContract::class);
        $this->app->bind(ProductContract::class, FilterForProductContract::class);
        $this->app->bind(ProductControllerContract::class, ProductControllerRepository::class);
        $this->app->bind(GetProductsInterface::class, GetProductsRepository::class);
        $this->app->bind(GetCollectionsInterface::class, GetCollectionsRepository::class);
        $this->app->bind(VendorProfileContract::class, VendorProfileRepository::class);
        $this->app->bind(OrderControllerRepositoryInterface::class, OrderControllerRepository::class);
        $this->app->bind(AdminContract::class, FilterForAdminContract::class);
        $this->app->bind(AdminControllerInterface::class, AdminControllerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading();
    }
}